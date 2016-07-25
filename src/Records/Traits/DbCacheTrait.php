<?php

trait App_Records_DbCacheTrait
{
    protected $_records = null;
    protected $_localCache;
    

     /**
     * @return Category_Abstract
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
    
    public function getCachedAll($params = array()) {        
        $cacheManager = $this->getCacheManager();
        $itemsCache = $cacheManager->get('all');
        
//        var_dump($items);

        if (is_array($itemsCache)) {
            $class = $this->getCollectionClass();
            $items = new $class();
                        
            foreach ($itemsCache as $itemCache) {
                $item = $this->getNew($itemCache);           
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
    
}