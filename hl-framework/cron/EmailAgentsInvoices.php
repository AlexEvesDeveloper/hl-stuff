<?php

final class Cron_EmailAgentsInvoices
{
    public function run() {
        $month = Zend_Registry::get('invoice_month');
        $year = Zend_Registry::get('invoice_year');

        $invoice = new Datasource_Core_Agent_Invoice();
        $email_status = new Datasource_Core_Agent_InvoiceEmailStatus();
        $agent_manager = new Manager_Core_Agent();

        // Get agent details that already invoiced for the reporting month
        $agents = $invoice->getAgentDetailsForInvoicing($month."_".$year);
        if ($agents) {
            foreach ($agents as $agent) {
                if ($agent['invoicesend'] === 'email' || $agent['invoicesend'] === 'both') {
                    if ($agent['email_address']) {
                        //if ($agent_manager->sendEmailNotification($agent['agentSchemeNo'], $agent['email_address'], $month, $year)) {
                        if ($agent['id']) {
                            if (!$agent['emailSent']) {
                                $agent_manager->sendEmailNotification($agent['agentSchemeNo'], $agent['email_address'], $month, $year);
                                $email_status->updateInvoiceEmailStatus($agent['agentSchemeNo']);
                            }
                        } else {
                            $agent_manager->sendEmailNotification($agent['agentSchemeNo'], $agent['email_address'], $month, $year);
                            $email_status->insertInvoiceEmailStatus($agent['agentSchemeNo'], $month, $year);
                        }
                        //}
                    }
                }
            }
        } else {
            echo("No agents to process\n");
        }
    }
}
