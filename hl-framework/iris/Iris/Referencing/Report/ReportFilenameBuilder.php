<?php

namespace Iris\Referencing\Report;

/**
 * Class ReportFilenameBuilder
 *
 * @package Iris\Referencing\Report
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
class ReportFilenameBuilder
{
    /**
     * Builds the report filename
     *
     * @param string $prefix
     * @param string $applicationReferenceNumber
     * @param string $extension
     * @return string
     */
    public function build($prefix, $applicationReferenceNumber, $extension = 'pdf')
    {
        return sprintf('%s-%s-%s.%s', $prefix, $applicationReferenceNumber, date('dmyHis'), $extension);
    }
}