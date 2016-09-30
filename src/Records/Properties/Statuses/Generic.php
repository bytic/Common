<?php

namespace ByTIC\Common\Records\Properties\Statuses;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as GenericProperty;
use ByTIC\Common\Records\Traits\HasStatus\RecordsTrait;

/**
 * Class Generic
 * @package ByTIC\Common\Records\Statuses
 *
 * @method RecordsTrait getManager
 */
abstract class Generic extends GenericProperty
{

    /**
     * @var array
     */
    protected $next = [];

    /**
     * @var self[]
     */
    protected $nextStatuses = null;

    /**
     * @return bool|void
     */
    public function update()
    {
        $item = $this->getItem();
        if ($item) {
            $this->preStatusChange();
            /** @noinspection PhpUndefinedFieldInspection */
            $item->status = $this->getName();
            $this->preUpdate();
            $return = $item->saveRecord();
            $this->postUpdate();

            return $return;
        }

        return false;
    }

    public function preStatusChange()
    {
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
    }

    /**
     * @return self[]
     */
    public function getNextStatuses()
    {
        if ($this->nextStatuses == null) {
            $this->initNextStatuses();
        }

        return $this->nextStatuses;
    }

    public function initNextStatuses()
    {
        $statuses = [];
        foreach ($this->next as $next) {
            $statuses[] = clone $this->getManager()->getStatus($next);
        }

        $this->nextStatuses = $statuses;
    }

    /**
     * @return bool
     */
    public function needsAssessment()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getLabelSlug()
    {
        return 'statuses';
    }
}
