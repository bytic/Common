<?php

namespace ByTIC\Common\Records\Properties\Statuses;

use ByTIC\Common\Records\Properties\AbstractProperty\Generic as GenericProperty;
use ByTIC\Common\Records\Traits\HasStatus\RecordsTrait;

/**
 * Class Generic
 * @package ByTIC\Common\Records\Statuses
 * @method RecordsTrait getManager
 * @deprecated Use \ByTIC\Models\SmartProperties\Properties\Statuses\Generic
 */
abstract class Generic extends GenericProperty
{

    /**
     * @var null|string
     */
    protected $field = 'status';

    /**
     * @var array
     */
    protected $next = [];

    /**
     * @var self[]
     */
    protected $nextStatuses = null;

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
