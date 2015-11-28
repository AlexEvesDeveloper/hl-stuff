<?php

namespace RRP\Cron;

use RRP\Application\Decorators\RentRecoveryPlusInsight;
use RRP\Application\Decorators\RentRecoveryPlusPolicy;
use RRP\DependencyInjection\LegacyContainer;
use RRP\DependencyInjection\RRPContainer;

/**
 * Class RentRecoveryPlusEmailReminders
 *
 * @package RRP\Cron
 * @author April Portus <april.portus@barbon.com>
 */
final class RentRecoveryPlusEmailReminders
{
    /**
     * Inception survey identifier - must match the connect.settings.survey.[inception].surveyID in connect.ini
     */
    const INCEPTION_SURVEY = 'inception';

    /**
     * Renewal survey identifier - must match the connect.settings.survey.[renewal].surveyID in connect.ini
     */
    const RENEWAL_SURVEY = 'renewal';

    /**
     * The survey provider's login page title
     */
    const SURVEY_LOGIN_PAGE_TITLE = 'Login - SmartSurvey Control Panel';

    /**
     * The survey provider's report page title
     */
    const SURVEY_REPORT_PAGE_TITLE = 'Object moved';

    /**
     * @var string - login username for survey provider
     */
    private $username;

    /**
     * @var string - login password for survey provider
     */
    private $password;

    /**
     * @var string - user agent (i.e. browser impersonator)
     */
    private $userAgent;

    /**
     * @var string - cookie file
     */
    private $cookieFile;

    /**
     * @var string - login URL
     */
    private $loginUrl;

    /**
     * @var string - login query string to append onto the login URL
     */
    private $loginQuery;

    /**
     * @var string - report URL
     */
    private $reportUrl;

    /**
     * @var string - survey file URL
     */
    private $csvFileUrl;

    /**
     * @var int - sleep time in seconds
     */
    private $sleepTime;

    /**
     * @var string - CSV file name including path
     */
    private $csvFile;

    /**
     * @var string - format of dates within the csv file for use with DateTime->createFromFormat
     */
    private $dateFormat;

    /**
     * @var array
     */
    private $surveyDetails;

    /**
     * @var resource - curl handle
     */
    private $curlResource;

    /**
     * Run the cron job to process the survey results and send the emails
     *
     * @throws \Exception
     */
    public function run()
    {
        $this->initialiseParams();

        $container = new LegacyContainer();
        /** @var \Datasource_Insurance_RentRecoveryPlus_Search $searchClient */
        $searchClient = $container->get('rrp.legacy.datasource.search');

        $policyNotes = $container->get('rrp.legacy.datasource.policy_notes');

        /** @var \Datasource_Insurance_RentRecoveryPlus_RentRecoveryPlus $rrp */
        $rrp = $container->get('rrp.legacy.datasource.rent_recovery_plus');

        $policyNumberManager = $container->get('rrp.legacy.manager.policy_number');

        $now = new \DateTime();

        // initialise curl options
        $this->initialiseCurl();

        // Attempt login
        $this->loginQuery = str_replace('{$username}', $this->username, $this->loginQuery);
        $this->loginQuery = str_replace('{$password}', $this->password, $this->loginQuery);
        $result = $this->executeCurlRequest($this->loginUrl, $this->loginUrl, $this->loginQuery);

        // Get the title and validate we are on the right page
        preg_match('|<title>(.*)</title>|', $result, $matches);
        $title = $matches[1];

        if ($title == self::SURVEY_LOGIN_PAGE_TITLE) {
            // Back to login page means we failed to login
            $message = 'Login failure!';
            curl_close($this->curlResource);
            error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
            throw new \Exception($message);
        }
        else if ($title != self::SURVEY_REPORT_PAGE_TITLE) {
            // Check we're on the right page - if not they've change something!
            $message = 'Unknown login response [' . $title . ']!';
            curl_close($this->curlResource);
            error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
            throw new \Exception($message);
        }


        // Login was a success, now process the surveys
        $previousSurveyID = 0;
        $fileData = array();

        foreach ($this->surveyDetails as $surveyType => $surveyDetail) {

            if ($previousSurveyID != $surveyDetail['surveyID']) {
                $previousSurveyID = $surveyDetail['surveyID'];

                // Get the report ID
                $reportData = str_replace('{$surveyID}', $surveyDetail['surveyID'], $surveyDetail['reportData']);
                $result = $this->executeCurlRequest($this->reportUrl, $this->loginUrl, $reportData);

                // Below is an excerpt from the inception result so we can see what to match
                //"Exporting Data...","download.asp?r=119595&i=112064&TB_iframe=true&height=270&width=400",null);
                $reportMatch = str_replace('{$surveyID}', $surveyDetail['surveyID'], $surveyDetail['reportMatch']);
                preg_match($reportMatch, $result, $matches);
                if (count($matches) <= 0) {
                    $message = 'Failed to find report ID for survey :' . $surveyDetail['surveyID'];
                    curl_close($this->curlResource);
                    error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
                    throw new \Exception($message);
                }

                $reportID = $matches[1];
                if ( ! is_numeric($reportID)) {
                    $message = 'Invalid report ID:[' . $reportID . ']';
                    curl_close($this->curlResource);
                    error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
                    throw new \Exception($message);
                }

                // Have a quick snooze and a cuppa while we wait for the report
                sleep($this->sleepTime);

                // Now attempting to get the CSV file location
                $csvFileData = str_replace('{$surveyID}', $surveyDetail['surveyID'], $surveyDetail['csvFileData']);
                $csvFileData = str_replace('{$reportID}', $reportID, $csvFileData);
                $result = $this->executeCurlRequest($this->csvFileUrl, $this->reportUrl, $csvFileData);

                // Below is an excerpt from the inception result so we can see what to match
                //<body><h1>Object Moved</h1>This object may be found <a HREF="https://www.smartsurvey.co.uk/_files/data/RawData--112064-37124-19-08-2014.csv">here</a>.</body>

                preg_match($surveyDetail['csvFileMatch'], $result, $matches);
                if (count($matches) <= 0) {
                    $message = 'Failed to extract the CSV file location';
                    curl_close($this->curlResource);
                    error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
                    throw new \Exception($message);
                }
                // Extract the generated csv file name
                $remoteCsvUrl = $matches[1];

                // Do the actual download
                try {
                    $this->executeCurlDownload($remoteCsvUrl, $this->csvFile);
                } catch (\Exception $ex) {
                    curl_close($this->curlResource);
                    error_log(__FILE__ . ':' . __LINE__ . ':' . $ex->getMessage());
                    throw new \Exception($ex->getMessage());
                }

                // Download successful, now parse the file
                $header = NULL;
                $fileData = array();
                if (($fileResource = fopen($this->csvFile, 'r')) !== FALSE) {
                    while (($row = fgetcsv($fileResource, 1000, $surveyDetail['delimiter'], '"', '\\')) !== FALSE) {
                        if (!$header) {
                            $header = $row;
                        }
                        else {
                            $fileData[] = array_combine($header, $row);
                        }
                    }
                    fclose($fileResource);
                }
                else {
                    $message = 'Failed to open CSV file';
                    error_log(__FILE__ . ':' . __LINE__ . ':' . $message());
                    throw new \Exception($message());
                }
            }

            // Parse the file and extract the policy numbers and dates
            $surveyDates = array();
            $line = 0;
            $policyColumnName = $surveyDetail['policyColumnName'];
            $completeColumnName = $surveyDetail['completeColumnName'];
            foreach ($fileData as $row) {
                $line++;
                $policyNumber = trim(urldecode($row[$policyColumnName]));
                $completeDate = \DateTime::createFromFormat($this->dateFormat, $row[$completeColumnName]);

                if ($policyNumberManager->isRentRecoveryPlusInsightPolicy($policyNumber)) {
                    //Ignore old Insight RRPI policies
                }
                else if ( ! $policyNumberManager->isRentRecoveryPlusPolicy($policyNumber)) {
                    $message = 'Bad Policy ID [' . $policyNumber . '] on line ' . $line;
                    error_log(__FILE__ . ':' . __LINE__ . ':' . $message);
                }
                else {
                    $surveyDates[$policyNumber] = $completeDate->format('Y-m-d H:i:s');
                }
            }

            // Log the complete dates for any newly completed ones
            $policiesRequiringNotes = $searchClient->searchForUncompletedSurveys(array_keys($surveyDates));
            foreach ($policiesRequiringNotes as $details) {
                $policyNumber = $details['policyNumber'];

                // Log the survey date
                if (array_key_exists($policyNumber, $surveyDates)) {
                    $rrp->setSurveyCompletedAt($policyNumber, $surveyDates[$policyNumber]);

                    // Add a note to the policy
                    $policyNotes->addNote(
                        $policyNumber,
                        $now->format('d/m/Y') . "\nSurvey sent to " . $details['emailAddress'] . " was completed\n"
                    );
                }
            }

            // Send the survey reminder emails
            foreach ($surveyDetail['emailDuration'] as $duration) {
                if (0 == $duration) {
                    continue;
                }

                //Select a list of email addresses where the survey has not been completed and the policy was
                // incepted {$duration} days ago

                $searchDate = clone $now;
                $searchDate->modify('-' . $duration . ' Day');
                $isInception = (self::INCEPTION_SURVEY == $surveyType);
                $reminders = $searchClient->searchForSurveyEmails($searchDate, $isInception, array_keys($surveyDates));

                foreach ($reminders as $reminderDetails) {

                    $landlordName = $reminderDetails['landlordName'];
                    $lettingAgent = $reminderDetails['lettingAgent'];
                    $policyNumber = $reminderDetails['policyNumber'];
                    $surveyLink = str_replace('{$policyNumber}', urlencode($policyNumber), $surveyDetail['emailSurveyLink']);
                    $subject = str_replace('{$lettingAgent}', $lettingAgent, $surveyDetail['emailSubject']);

                    $mail = $container->get('rrp.legacy.mailer');
                    $mail->setTo($reminderDetails['emailAddress'], null);
                    $mail->setFrom($surveyDetail['emailFrom'], 'HomeLet');
                    $mail->setSubject($subject);

                    // Apply template
                    $mail->applyTemplate(
                        'rent-recovery-plus/survey-reminder',
                        array(
                            'landlordName' => $landlordName,
                            'lettingAgent' => $lettingAgent,
                            'surveyLink' => $surveyLink,
                        ),
                        true
                    );

                    $mail->applyTextTemplate(
                        'rent-recovery-plus/survey-reminder-txt',
                        array(
                            'landlordName' => $landlordName,
                            'lettingAgent' => $lettingAgent,
                            'surveyLink' => $surveyLink,
                        ),
                        true
                    );

                    // Send email
                    $mail->send();

                    $policyNotes->addNote(
                        $policyNumber,
                        $now->format('d/m/Y') . "\nSurvey sent to " . $reminderDetails['emailAddress'] . "\n"
                    );
                }
            }
        }
        curl_close($this->curlResource);
    }

    /**
     * Initialise from the params file
     */
    private function initialiseParams()
    {
        $rrpContainer = new RRPContainer();

        // Initialise from config file
        $this->username   = $rrpContainer->get('rrp.config.survey.username');
        $this->password   = $rrpContainer->get('rrp.config.survey.password');
        $this->userAgent  = $rrpContainer->get('rrp.config.survey.userAgent');
        $this->cookieFile = $rrpContainer->get('rrp.config.survey.cookieFile');
        $this->loginUrl   = $rrpContainer->get('rrp.config.survey.loginUrl');
        $this->loginQuery = $rrpContainer->get('rrp.config.survey.loginQuery');
        $this->reportUrl  = $rrpContainer->get('rrp.config.survey.reportUrl');
        $this->csvFileUrl = $rrpContainer->get('rrp.config.survey.csvFileUrl');
        $this->sleepTime  = $rrpContainer->get('rrp.config.survey.sleepTime');
        $this->csvFile    = $rrpContainer->get('rrp.config.survey.csvFile');
        $this->dateFormat = $rrpContainer->get('rrp.config.survey.dateFormat');

        $this->surveyDetails = array();
        foreach (array(self::INCEPTION_SURVEY, self::RENEWAL_SURVEY) as $surveyType) {
            $this->surveyDetails[$surveyType] = array(
                'surveyID'         => $rrpContainer->get(sprintf('rrp.config.survey.%s.survey_id', $surveyType)),
                'reportData'       => $rrpContainer->get(sprintf('rrp.config.survey.%s.report_data', $surveyType)),
                'reportMatch'      => $rrpContainer->get(sprintf('rrp.config.survey.%s.report_match', $surveyType)),
                'csvFileData'      => $rrpContainer->get(sprintf('rrp.config.survey.%s.csv_file_data', $surveyType)),
                'csvFileMatch'     => $rrpContainer->get(sprintf('rrp.config.survey.%s.csv_file_match', $surveyType)),
                'delimiter'        => $rrpContainer->get(sprintf('rrp.config.survey.%s.delimiter', $surveyType)),
                'policyColumnName' =>
                    $rrpContainer->get(sprintf('rrp.config.survey.%s.policy_column_name', $surveyType)),
                'completeColumnName' =>
                    $rrpContainer->get(sprintf('rrp.config.survey.%s.complete_column_name', $surveyType)),
                'emailDuration' => array(
                    'First'        => $rrpContainer->get(sprintf('rrp.config.survey.%s.email_duration1', $surveyType)),
                    'Second'       => $rrpContainer->get(sprintf('rrp.config.survey.%s.email_duration2', $surveyType))
                ),
                'emailFrom'        => $rrpContainer->get(sprintf('rrp.config.survey.%s.email_from', $surveyType)),
                'emailSubject'     => $rrpContainer->get(sprintf('rrp.config.survey.%s.email_subject', $surveyType)),
                'emailSurveyLink'  => $rrpContainer->get(sprintf('rrp.config.survey.%s.email_surveyLink', $surveyType))
            );
        }
    }

    /**
     * Initialise the curl options
     */
    private function initialiseCurl()
    {
        $this->curlResource = curl_init();
        curl_setopt($this->curlResource, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curlResource, CURLOPT_TIMEOUT, 60);
        curl_setopt($this->curlResource, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->curlResource, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curlResource, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($this->curlResource, CURLOPT_COOKIEJAR, $this->cookieFile);
    }

        /**
     * Executes the curl request and returns the results
     *
     * @param string $url
     * @param string $referrer
     * @param string $postFields
     * @param int $isPost
     * @return mixed
     */
    private function executeCurlRequest($url, $referrer, $postFields, $isPost=1)
    {
        curl_setopt($this->curlResource, CURLOPT_URL, $url);
        curl_setopt($this->curlResource, CURLOPT_REFERER, $referrer);
        curl_setopt($this->curlResource, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($this->curlResource, CURLOPT_POST, $isPost);
        return curl_exec($this->curlResource);
    }

    /**
     * Download the file using curl
     *
     * @param string $remoteCsvUrl
     * @param string $fileName
     * @throws \Exception
     */
    private function executeCurlDownload($remoteCsvUrl, $fileName)
    {
        $fileResource = fopen($fileName, 'w+');
        $fileCurlResource = curl_init();
        curl_setopt($fileCurlResource, CURLOPT_URL, $remoteCsvUrl);
        curl_setopt($fileCurlResource, CURLOPT_TIMEOUT, 50);
        curl_setopt($fileCurlResource, CURLOPT_FILE, $fileResource);
        curl_setopt($fileCurlResource, CURLOPT_FOLLOWLOCATION, true);
        if (!curl_exec($fileCurlResource)) {
            fclose($fileResource);
            curl_close($fileCurlResource);
            throw new \Exception('Failed to download the CSV');
        }
        fclose($fileResource);
        curl_close($fileCurlResource);
    }
}
