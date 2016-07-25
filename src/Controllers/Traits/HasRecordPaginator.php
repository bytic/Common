<?php

namespace ByTIC\Common\Controllers\Traits;

trait HasRecordPaginator
{

    public $paginator = null;

    public function getRPaginator()
    {
        if ($this->paginator === null) {
            $this->initRPaginator();
        }
        return $this->paginator;
    }
    
    public function initRPaginator()
    {
        $this->paginator = $this->newRPaginator();
    }

    public function newRPaginator()
    {
        return new \Nip_Record_Paginator();
    }

}