<?php

namespace ByTIC\Common\Controllers\Traits;

use ByTIC\Common\Application\Controllers\Traits\AbstractControllerTrait;
use Nip\Request;
use Nip_Record_Paginator as RecordPaginator;

/**
 * Class HasRecordPaginator
 *
 * @package ByTIC\Common\Controllers\Traits
 * @method Request getRequest()
 */
trait HasRecordPaginator
{
    use AbstractControllerTrait;
    /**
     * Record Paginator Object
     *
     * @var null|RecordPaginator
     */
    protected $paginator = null;

    /**
     * Get Record Paginator Object
     *
     * @return RecordPaginator
     */
    public function getRecordPaginator()
    {
        if ($this->paginator === null) {
            $this->initRecordPaginator();
        }

        return $this->paginator;
    }

    /**
     * Init Record Paginator Object
     *
     * @return void
     */
    public function initRecordPaginator()
    {
        $this->setRecordPaginator($this->newRecordPaginator());
        $this->prepareRecordPaginator();
    }

    /**
     * Set the Record Paginator
     *
     * @param RecordPaginator $paginator Record Paginator Object
     *
     * @return $this
     */
    public function setRecordPaginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Generates a new instance of Record Paginator
     *
     * @return RecordPaginator
     */
    public function newRecordPaginator()
    {
        return new \Nip_Record_Paginator();
    }

    /**
     * Prepare Record Paginator Object
     *
     * @return void
     */
    public function prepareRecordPaginator()
    {
        $page = $this->getRequest()->get('page', 1);
        $this->getRecordPaginator()->setPage(intval($page));
        $this->getRecordPaginator()->setItemsPerPage(50);
    }
}
