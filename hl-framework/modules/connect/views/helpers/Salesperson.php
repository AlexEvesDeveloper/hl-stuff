<?php

class Connect_View_Helper_Salesperson extends Zend_View_Helper_Abstract
{

    public function salesperson($asn, $size = 'small') {

        // First get agent and agent's salesperson ID
        $agentManager = new Manager_Core_Agent();
        $agent = $agentManager->getAgent($asn);

        // Look up salesperson by ID
        $salesPersonManager = new Manager_Core_Salesperson();
        $salesperson = $salesPersonManager->getSalesperson($agent->salespersonId);

        // Convert date answer into since string
        foreach($salesperson->questionAnswers as $key => $questionAnswer) {

            // Look out for date-only answer, needs to be converted into
            // an "x ago" friendly string
            if (preg_match('/^\d{4}-\d\d-\d\d$/', $questionAnswer->answer) > 0) {
                $salesperson->questionAnswers[$key]->answer = $this->_dateToSinceString($questionAnswer->answer);
            }
        }

        return $this->view->partial(
            'partials/salesperson.phtml',
            array(
                'agent' => $agent,
                'salesperson' => $salesperson,
                'size' => $size
            )
        );
    }

    private function _dateToSinceString($date) {

        // Convert date to UNIX epoch seconds
        $start_unixtime = mktime(0, 0, 0, sprintf('%d', substr($date, 5, 2)), sprintf('%d', substr($date, 8, 2)), substr($date, 0, 4)); // The sprintfs remove leading zeros that mktime thinks denote octal

        // Convert today to UNIX epoch seconds
        $end_unixtime = mktime(0, 0, 0, date('n'), date('j'), date('Y'));

        // Difference in seconds
        $diff = $end_unixtime - $start_unixtime;

        // Difference in days
        $diff = $diff / 86400;

        // If difference is under one year, report in months
        if ($diff < 365) {
            return sprintf('%d month%s', round($diff / 30.42, 0), (round($diff / 30.42, 0) != 1) ? 's' : '');
        } else {
            // If difference is more than 10 years, report in full years
            if ($diff > 3650) {
                return sprintf('%d years', round($diff / 365, 0));
            } else {
                // Difference is between 1 and 10 years, report to nearest half year
                $fullyears = round($diff / 365);
                $halfyear = (($diff / 365 - $fullyears) > 0.3) ? '.5' : '';
                return sprintf('%d%s year%s', $fullyears, $halfyear, (($fullyears == 1 && $halfyear == '') ? '' : 's'));
            }
        }
    }
}