<?php

namespace ByTIC\Common\Records;

use ByTIC\Common\Records\Traits\HasForms\RecordTrait as HasFormRecordTrait;
use Nip\Records\Record as NipRecord;
use Nip_Registry;

/**
 * Class Record
 *
 * @property int $id
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
     * @var Nip_Registry
     */
    protected $registry;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Nip_Registry
     */
    public function getRegistry()
    {
        if (!$this->registry) {
            $this->registry = new Nip_Registry();
        }

        return $this->registry;
    }

    /**
     * @param $action
     * @param array $params
     * @param null $module
     * @return mixed
     */
    public function compileURL($action, $params = [], $module = null)
    {
        $params = $this->injectURLParams($action, $params, $module);
        $this->filterURLParams($params);

        $action = ucfirst($action);

        return $this->getManager()->{"get".$action."URL"}($params, $module);
    }

    /**
     * @param $action
     * @param $params
     * @param null $module
     */
    public function injectURLParams($action, $params, $module = null)
    {
        $params = $this->injectUrlPK($action, $params, $module = null);

        return $params;
    }

    /**
     * @param $params
     */
    public function filterURLParams($params)
    {
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
}
