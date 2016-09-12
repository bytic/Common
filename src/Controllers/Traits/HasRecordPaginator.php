<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip_Record_Paginator as RecordPaginator;

trait HasRecordPaginator
{

    /**
     * @var null|RecordPaginator
     */
    protected $paginator = null;

    public function getRecordPaginator()
    {
        if ($this->paginator === null) {
            $this->initRecordPaginator();
        }
        return $this->paginator;
    }

    public function initRecordPaginator()
    {
        $this->setRecordPaginator($this->newRecordPaginator());
        $this->prepareRecordPaginator();
    }

    public function prepareRecordPaginator()
    {
        $this->getRecordPaginator()->setPage(intval($_GET['page']));
        $this->getRecordPaginator()->setItemsPerPage(50);
    }

    /**
     * @param RecordPaginator $paginator
     * @return $this
     */
    public function setRecordPaginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @return RecordPaginator
     */
    public function newRecordPaginator()
    {
        return new \Nip_Record_Paginator();
    }

}