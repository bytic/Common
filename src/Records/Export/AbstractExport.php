<?php

namespace ByTIC\Common\Records\Export;

use ByTIC\Common\Records\Records;
use Nip\Database\Query\Select;
use Nip\Records\Collections\Collection as RecordCollection;

class AbstractExport
{
    /**
     * @var Records
     */
    protected $manager;

    /**
     * @var Select
     */
    protected $query;

    /**
     * @var RecordCollection
     */
    protected $items;

    protected $itemsData;
    protected $data;

    public function getWrapper()
    {
    }

    public function generate()
    {
        ini_set('memory_limit', '256M');
        $this->populateItems();
        $this->generateItemsData();
        $this->generateData();
    }

    public function populateItems()
    {
        $this->items = $this->getManager()->findByQuery($this->getQuery());

        if (count($this->items)) {
            $this->hydrateItems();
        }
    }

    /**
     * @return Records
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param Records $item
     * @return $this
     */
    public function setManager($item)
    {
        $this->manager = $item;
        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    protected function hydrateItems()
    {
    }

    public function generateItemsData()
    {
    }

}