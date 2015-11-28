<?php

namespace Iris\Common;

/**
 * Class UploadableFileConstraintParameters
 *
 * @package Iris\Common
 * @author Ashley Dawson <ashley.dawson@barbon.com>
 */
final class UploadableFileConstraintParameters
{
    /**
     * Get maximum file size upload limit
     *
     * @return string
     */
    public static function getMaxUploadFileSize()
    {
        return '2M';
    }

    /**
     * Get invalid mime type message
     *
     * @return string
     */
    public static function getInvalidMimeTypeMessage()
    {
        return 'Only PDF, MSWord, MSExcel, Plain Text or Image files are allowed';
    }

    /**
     * Get allowed mime types
     *
     * @return array
     */
    public static function getAllowedMimeTypes()
    {
        return array(
            'application/pdf',
            'application/x-pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel.addin.macroEnabled.12',
            'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'application/vnd.ms-excel.sheet.macroEnabled.12',
            'application/vnd.ms-excel.template.macroEnabled.12',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/zip',
            'application/vnd.ms-office',
            'text/plain',
            'text/enriched',
            'text/richtext',
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/bmp',
            'image/tiff',
            'image/tiff-fx',
        );
    }
}