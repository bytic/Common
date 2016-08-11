<?php

namespace ByTIC\Common\Controllers\Traits;

use Nip\Records\_Abstract\Row;
use Nip\Records\_Abstract\Table;
use Nip\View;

/**
 * Class CrudModels
 * @package ByTIC\Common\Controllers\Traits
 *
 * @method Table getModelManager()
 * @method View getView()
 * @method Row getModelFromRequest($key = false)
 */
trait CrudModels
{
    protected $_urls = array();
    protected $_flash = array();

    protected function beforeAction()
    {
        parent::beforeAction();
        $this->getView()->section = inflector()->underscore($this->getModel());
    }

    protected function afterAction()
    {
        if (!$this->getView()->modelManager) {
            $this->getView()->modelManager = $this->getModelManager();
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

    /**
     * @param bool|Row $item
     */
    protected function setItemBreadcrumbs($item = false)
    {
        $item = $item ? $item : $this->getModelFromRequest();
        $this->getView()->Breadcrumbs()->addItem($item->getName(), $item->getURL());

        $this->getView()->Meta()->prependTitle($item->getName());
    }


    public function index()
    {
        $this->query = $this->query ? $this->query : $this->newIndexQuery();
        $this->filters = $this->filters ? $this->filters : $this->getModelManager()->requestFilters($_GET);
        $this->query = $this->getModelManager()->filter($this->query, $this->filters);

        $this->paginator = $this->paginator ? $this->paginator : new \Nip_Record_Paginator();

        $this->paginator->setPage(intval($_GET['page']));
        $this->paginator->setItemsPerPage(50);
        $this->paginator->paginate($this->query);

        if ($this->items) {
        } else {
            $this->items = $this->getModelManager()->findByQuery($this->query);
            $this->paginator->count();
        }

        $this->getView()->items = $this->items;
        $this->getView()->filters = $this->filters;
        $this->getView()->title = $this->getModelManager()->getLabel('title');

        $this->getView()->Paginator()->setPaginator($this->paginator)->setURL($this->getModelManager()->getURL());
    }

    protected function newIndexQuery()
    {
        return $this->getModelManager()->paramsToQuery();
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

    /**
     * @param Row $item
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

    public function newModel()
    {
        return $this->getModelManager()->getNew();
    }

    /**
     * @param Row $item
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
        $this->initExistingItem();

        $this->clone = clone $this->item;
        $this->form = $this->getModelForm($this->clone);

        $this->processView();

        $this->getView()->item = $this->item;
        $this->getView()->clone = $this->clone;
        $this->getView()->form = $this->form;
        $this->getView()->title = $this->item->getName();

        $this->getView()->section .= ".view";
        $this->getView()->TinyMCE()->setEnabled();

        $this->setItemBreadcrumbs();
        $this->postView();
    }

    public function edit()
    {
        $this->initExistingItem();

        $this->clone = clone $this->item;
        $this->form = $this->getModelForm($this->clone);

        $this->processView();

        $this->getView()->item = $this->item;
        $this->getView()->clone = $this->clone;
        $this->getView()->form = $this->form;
        $this->getView()->title = $this->item->getName();

        $this->getView()->section .= ".edit";
        $this->getView()->TinyMCE()->setEnabled();

        $this->setItemBreadcrumbs();
    }

    public function processView()
    {
        if ($this->form->submited() && $this->form->validate()) {
            $this->form->process();

            return $this->viewRedirect();
        }
    }

    public function viewRedirect()
    {
        $url = $this->_urls['after-edit'] ? $this->_urls['after-edit'] : $this->item->getURL()."#details";
        $flashName = $this->_flash["after-edit"] ? $this->_flash['after-edit'] : $this->getView()->controller;
        $this->flashRedirect($this->getModelManager()->getMessage('update'), $url, 'success', $flashName);
    }

    public function postView()
    {
        $this->setItemBreadcrumbs();
    }

    public function duplicate()
    {
        $this->initExistingItem();

        $this->item->duplicate();

        $url = $this->_urls["after-duplicate"] ? $this->_urls['after-duplicate'] : $this->getModelManager()->getURL();
        $this->_flashName = $this->_flashName ? $this->_flashName : $this->getModelManager()->getController();
        $this->flashRedirect($this->getModelManager()->getMessage('duplicate'), $url, 'success', $this->_flashName);
    }

    public function delete()
    {
        $this->initExistingItem();

        $this->item->delete();
        $this->deleteRedirect();
    }

    public function deleteRedirect()
    {
        $url = $this->_urls["after-delete"] ? $this->_urls['after-delete'] : $this->getModelManager()->getURL();
        $flashName = $this->_flash["after-delete"] ? $this->_flash['after-delete'] : $this->getModelManager()->getController();
        $this->flashRedirect($this->getModelManager()->getMessage('delete'), $url, 'success', $flashName);
    }

    public function activate()
    {
        $this->initExistingItem();

        $this->item->activate();
        $this->flashRedirect($this->getModelManager()->getMessage('activate'), $this->item->getURL());
    }

    public function deactivate()
    {
        $this->initExistingItem();

        $this->item->deactivate();
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

    /**
     * @return Row
     */
    protected function initExistingItem()
    {
        if (!$this->item) {
            $this->item = $this->getModelFromRequest();
        }
        return $this->item;
    }
}