<?php

namespace ByTIC\Common\Records;

use ByTIC\Common\Records\Traits\HasForms\RecordTrait as HasFormRecordTrait;
use Nip\Collections\Registry;
use Nip\Records\Record as NipRecord;

/**
 * Class Record
 *
 * @property int $id
 * @property string $name
 * @property string $modified
 * @property string $created
 *
 * @method string getURL($params = [], $module = null)
 * @method Records getManager()
 */
class Record extends NipRecord
{
    use HasFormRecordTrait;

    protected $_urlPK;
    protected $_urlCol = 'name';
    /**
     * @var Registry
     */
    protected $registry = null;

    /**
     * @inheritdoc
     */
    public function __call($name, $arguments = [])
    {
        /** @noinspection PhpAssignmentInConditionInspection */
        if ($return = $this->isCallUrl($name, $arguments)) {
            return $return;
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool
     */
    protected function isCallUrl($name, $arguments)
    {
        if (substr($name, 0, 3) == "get" && substr($name, -3) == "URL") {
            $action = substr($name, 3, -3);
            $action = (!empty($action)) ? $action : 'View';
            $params = isset($arguments[0]) ? $arguments[0] : null;
            $module = isset($arguments[1]) ? $arguments[1] : null;

            return $this->compileURL($action, $params, $module);
        }

        return false;
    }

    /**
     * @param $action
     * @param array $params
     * @param null $module
     * @return mixed
     */
    public function compileURL($action, $params = [], $module = null)
    {
        $manager = $this->getManager();

        if ($manager->hasRelation($action)) {
            $relation = $manager->getRelation($action);
            $manager = $relation->getWith();
            $action = 'index';
            $params[$this->getManager()->getPrimaryFK()] = $this->getPrimaryKey();
        } else {
            $params = $this->injectURLParams($action, $params, $module);
            $this->filterURLParams($params);
        }

        return $manager->compileURL($action, $params, $module);
    }

    /**
     * @param $action
     * @param $params
     * @param null $module
     */
    public function injectURLParams($action, $params, $module = null)
    {
        $params = $this->injectUrlPK($action, $params, $module);

        return $params;
    }

    /**
     * @param $action
     * @param $params
     * @param null $module
     */
    protected function injectUrlPK($action, $params, $module = null)
    {
        $pk = $this->getManager()->getUrlPK();
        if (is_array($pk)) {
            foreach ($pk as $field) {
                $params[$field] = $this->{$field};
            }
        } else {
            $params[$pk] = $this->{$pk};
        }

        return $params;
    }

    /**
     * @param $params
     */
    public function filterURLParams($params)
    {
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        if ($this->registry === null) {
            $this->registry = new Registry();
        }

        return $this->registry;
    }
}
