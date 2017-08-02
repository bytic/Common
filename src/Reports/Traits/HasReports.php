<?php

namespace ByTIC\Common\Reports\Traits;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordsTrait;

/**
 * Class RecordHasReports
 *
 * @package ByTIC\Common\Reports\Generator
 */
trait RecordHasReports
{
    use RecordsTrait;

    /**
     * @param $name
     * @param $params
     */
    public function newReport($name, $params)
    {
        return;
    }

    protected abstract function getReportsModelManager();

    /**
     * @param $name
     * @return string
     */
    public function compileReportName($name)
    {
        return $name;
    }

}
