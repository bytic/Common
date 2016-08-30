<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Records\Record;
use Nip\Records\RecordManager;
use Nip\Request;
use Nip\View;
use Nip_Form_Model as Form;
use Nip_Record_Paginator as RecordPaginator;

/**
 * Class CrudModels
 * @package ByTIC\Common\Controllers\Traits
 *
 * @method string getModel()
 * @method RecordManager getModelManager()
 * @method View getView()
 * @method Request getRequest()
 * @method Form getModelForm($model, $action = null)
 * @method Record getModelFromRequest($key = false)
 * @method string flashRedirect($message, $url, $type = 'success', $name = false)
 */
trait CrudModels
{
    protected $_urls = array();
    protected $_flash = array();
    /**
     * @var null|RecordPaginator
     */
    protected $_paginator = null;

    public function index()
    {
        $query = $this->query ? $this->query : $this->newIndexQuery();
        $filters = $this->filters ? $this->filters : $this->getModelManager()->requestFilters($_GET);
        $query = $this->getModelManager()->filter($query, $filters);

        if ($this->paginator) {
            trigger_error('use paginator functions instead of paginator attribute', E_USER_DEPRECATED);
            $this->setRecordPaginator($this->paginator);
        }
        $this->prepareRecordPaginator();
        $paginator = $this->getRecordPaginator();
        $paginator->paginate($query);

        if ($this->items) {
            $items = $this->items;
        } else {
            $items = $this->getModelManager()->findByQuery($query);
            $paginator->count();
        }

        $this->getView()->set('items', $items);
        $this->getView()->set('filters', $filters);
        $this->getView()->set('title', $this->getModelManager()->getLabel('title'));

        $this->getView()->Paginator()->setPaginator($paginator)->setURL($this->getModelManager()->getURL());
    }

    /**
     * @return \Nip\Database\Query\Select
     */
    protected function newIndexQuery()
    {
        return $this->getModelManager()->paramsToQuery();
    }

    /**
     * @param RecordPaginator $paginator
     * @return $this
     */
    public function setRecordPaginator($paginator)
    {
        $this->_paginator = $paginator;
        return $this;
    }

    public function prepareRecordPaginator()
    {
        $this->getRecordPaginator()->setPage(intval($_GET['page']));
        $this->getRecordPaginator()->setItemsPerPage(50);
    }

    /**
     * @return RecordPaginator
     */
    public function getRecordPaginator()
    {
        if ($this->_paginator == null) {
            $this->initRecordPaginator();
        }
        return $this->_paginator;
    }

    public function initRecordPaginator()
    {
        $this->setRecordPaginator($this->newRecordPaginator());
    }

    /**
     * @return RecordPaginator
     */
    public function newRecordPaginator()
    {
        return new RecordPaginator();
    }

    public function add()
    {

        $item = $this->addNewModel();
        $form = $this->addGetForm($item);

        if ($form->execute()) {
            $this->addRedirect($item);
        }

        $this->getView()->set('item', $item);
        $this->getView()->set('form', $form);
        $this->getView()->set('title', $this->getModelManager()->getLabel('add'));

        $this->getView()->Breadcrumbs()->addItem($this->getModelManager()->getLabel('add'));
        $this->getView()->TinyMCE()->setEnabled();
        $this->getView()->section .= ".add";
    }

    public function addNewModel()
    {
        $item = $this->item ? $this->item : $this->newModel();

        return $item;
    }

    public function newModel()
    {
        return $this->getModelManager()->getNew();
    }

    /**
     * @param Record $item
     * @return mixed
     */
    public function addGetForm($item)
    {
        if ($this->form) {
            $form = $this->form;
        } else {
            $form = $this->getModelForm($item);
            $form->setAction($this->getModelManager()->getAddURL($_GET));
        }

        return $form;
    }

    /**
     * @param Record $item
     * @return mixed
     */
    public function addRedirect($item)
    {
        $url = $this->_urls["after-add"] ? $this->_urls['after-add'] : $item->getURL();
        $flashName = $this->_flash["after-add"] ? $this->_flash['after-add'] : $this->getModelManager()->getController();

        return $this->flashRedirect($this->getModelManager()->getMessage('add'), $url, 'success', $flashName);
    }

    public function view()
    {
        $item = $this->initExistingItem();

        $this->clone = clone $item;
        $this->form = $this->getModelForm($this->clone);

        $this->processForm($this->form);

        $this->getView()->set('item', $item);
        $this->getView()->set('clone', $this->clone);
        $this->getView()->set('form', $this->form);
        $this->getView()->set('title', $item->getName());

        $this->getView()->append('section', ".view");
        $this->getView()->TinyMCE()->setEnabled();

        $this->setItemBreadcrumbs();
        $this->postView();
    }

    /**
     * @return Record
     */
    protected function initExistingItem()
    {
        if (!$this->item) {
            $this->item = $this->getModelFromRequest();
        }

        return $this->item;
    }

    /**
     * @param Form $form
     */
    public function processForm($form)
    {
        if ($form->execute()) {
            $this->viewRedirect($form->getModel());
        }
    }

    /**
     * @param Record|boolean $item
     */
    public function viewRedirect($item = null)
    {
        if ($item == null) {
            $item = $this->item;
            trigger_error('$item needed in viewRedirect', E_USER_DEPRECATED);
        }

        $url = $this->getAfterUrl('after-edit', $item->getURL());
        $flashName = $this->getAfterFlashName("after-edit", $this->getModelManager()->getControllerName());
        $this->flashRedirect($this->getModelManager()->getMessage('update'), $url, 'success', $flashName);
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function getAfterUrl($key, $default = null)
    {
        return isset($this->_urls[$key]) && $this->_urls[$key] ? $this->_urls[$key] : $default;
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return string
     */
    public function getAfterFlashName($key, $default = null)
    {
        return isset($this->_flash[$key]) && $this->_flash[$key] ? $this->_flash[$key] : $default;
    }

    /**
     * @param bool|Record $item
     */
    protected function setItemBreadcrumbs($item = false)
    {
        $item = $item ? $item : $this->getModelFromRequest();
        $this->getView()->Breadcrumbs()->addItem($item->getName(), $item->getURL());

        $this->getView()->Meta()->prependTitle($item->getName());
    }

    public function postView()
    {
        $this->setItemBreadcrumbs();
    }

    public function edit()
    {
        $item = $this->initExistingItem();

        $this->clone = clone $item;
        $this->form = $this->getModelForm($this->clone);

        $this->processForm($this->form);

        $this->getView()->set('item', $item);
        $this->getView()->set('clone', $this->clone);
        $this->getView()->set('form', $this->form);
        $this->getView()->set('title', $item->getName());

        $this->getView()->append('section', ".edit");
        $this->getView()->TinyMCE()->setEnabled();

        $this->setItemBreadcrumbs();
    }

    /**
     * @deprecated Use new processForm($form)
     */
    public function processView()
    {
        return $this->processForm($this->form);
    }

    public function duplicate()
    {
        $this->initExistingItem();

        $this->item->duplicate();

        $url = $this->getAfterUrl("after-duplicate", $this->getModelManager()->getURL());
        $flashName = $this->getAfterFlashName("after-duplicate", $this->getModelManager()->getController());
        $this->flashRedirect($this->getModelManager()->getMessage('duplicate'), $url, 'success', $flashName);
    }

    public function delete()
    {
        $item = $this->initExistingItem();

        $item->delete();
        $this->deleteRedirect();
    }

    public function deleteRedirect()
    {
        $url = $this->getAfterUrl("after-delete", $this->getModelManager()->getURL());
        $flashName = $this->getAfterFlashName("after-delete", $this->getModelManager()->getController());
        $this->flashRedirect($this->getModelManager()->getMessage('delete'), $url, 'success', $flashName);
    }

    public function activate()
    {
        $item = $this->initExistingItem();

        $item->activate();
        $this->flashRedirect($this->getModelManager()->getMessage('activate'), $this->item->getURL());
    }

    public function deactivate()
    {
        $item = $this->initExistingItem();

        $item->deactivate();
        $this->flashRedirect($this->getModelManager()->getMessage('deactivate'), $this->item->getURL());
    }

    public function inplace()
    {
        $item = $this->initExistingItem();

        $pk = $this->getModelManager()->getPrimaryKey();

        foreach ($this->getModelManager()->getFields() as $key) {
            if ($key != $pk && $_POST[$key]) {
                $field = $key;
            }
        }

        if ($field) {
            $item->getFromRequest($_POST, array($field));
            if ($item->validate()) {
                $item->save();
                $item->Async()->json(array(
                    "type" => "success",
                    "value" => $item->$field,
                    "message" => $this->getModelManager()->getMessage("update"),
                ));
            }
        }

        $this->Async()->json(array("type" => "error"));
    }

    public function uploadFile()
    {
        $item = $this->initExistingItem();

        $file = $item->uploadFile($_FILES['Filedata']);

        if ($file) {
            $response['type'] = "success";
            $response['url'] = $item->getFileURL($file);
            $response['name'] = $file->getName();
            $response['extension'] = $file->getExtension();
            $response['size'] = \Nip_File_System::instance()->formatSize($file->getSize());
            $response['time'] = date("d.m.Y H:i", $file->getTime());
        } else {
            $response['type'] = 'error';
        }

        $this->Async()->json($response);
    }

    protected function beforeAction()
    {
        parent::beforeAction();
        $this->getView()->set('section', inflector()->underscore($this->getModel()));
    }

    protected function afterAction()
    {
        if (!$this->getView()->has('modelManager')) {
            $this->getView()->set('modelManager', $this->getModelManager());
        }
        parent::afterAction();
    }

    protected function setClassBreadcrumbs($parent = false)
    {
        $this->getView()->Breadcrumbs()->addItem(
            $this->getModelManager()->getLabel('title'),
            $this->getModelManager()->getURL());
        $this->getView()->Meta()->prependTitle($this->getModelManager()->getLabel('title'));
    }

}