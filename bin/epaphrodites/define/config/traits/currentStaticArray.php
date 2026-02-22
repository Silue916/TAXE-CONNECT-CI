<?php

namespace Epaphrodites\epaphrodites\define\config\traits;

trait currentStaticArray {

    /**
     * List of allowed email domains
     *
     * @var array
     * @return array
     */
    protected static $EmailDomaine = ['gmail.com', 'yahoo.com', 'hotmail.com'];

    /**
     * List of allowed file extensions
     *
     * @var array
     * @return array
     */
    protected static $AllExtensions = ['ods', 'csv', 'xls', 'xlsx'];

    /**
     * List of allowed file MIME types
     *
     * @var array
     * @return array
     */
    protected static $FilesMimes = [
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
}
