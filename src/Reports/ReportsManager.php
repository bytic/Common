<?php

namespace ByTIC\Common\Reports;

use ByTIC\Common\Reports\Models\ReportsTrait;

/**
 * Class ReportsManager
 * @package ByTIC\Common\Reports
 */
class ReportsManager
{
    /**
     * The database connection instance.
     *
     * @var ReportsTrait
     */
    protected $reportsModelManager;


    /**
     * @param $name
     * @param $params
     */
    public static function push($name, $params)
    {
        return $this->pushToDatabase($queue, $this->createPayload($job, $data));
    }
}
