<?php

namespace ByTIC\Common\Records\Traits;

use Nip\Records\CacheManager;
use Nip\Records\Record;

trait DbCacheTrait
{
    protected $_cacheManager = null;

    protected $_records = null;
    protected $_localCache;

     /**
     * @return Record
     */
    public function findOne($primary) {
        if (!$this->_records) {
            $this->getAll();
        }
        return $this->_records[$primary];
    }

    public function getAll() {
        if (!$this->_records) {
            $this->_records = $this->getCachedAll();
        }
        return $this->_records;
    }
    
    public function getCachedAll() {
        $cacheManager = $this->getCacheManager();
        $itemsCache = $cacheManager->get('all');
        
//        var_dump($items);

        if (is_array($itemsCache)) {
            $items = $this->newCollection();
                        
            foreach ($itemsCache as $itemCache) {
                $item = $this->getNew();
                $item->writeData($itemCache);
                $items->add($item);
            }
            
        } else {
            // set cache
            $items = $this->findByParams();
            $this->initColection($items);
            $cacheData = array();
            foreach ($items as $key=>$item) {
                $cacheData[$key] = $item->toArray();
            }
            $cacheManager->saveData('all', $cacheData);            
        }
        
        return $items;
    }
    
    public function initColection(&$items)
    {
        return;
    }

    /**
     * @return CacheManager
     */
    public function getCacheManager()
    {
        if (!$this->_cacheManager) {
            $this->_cacheManager = new CacheManager();
            $this->_cacheManager->setManager($this);
        }

        return $this->_cacheManager;
    }
    
}