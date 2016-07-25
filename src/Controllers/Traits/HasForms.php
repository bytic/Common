<?php

namespace ByTIC\Common\Controllers\Traits;

trait HasForms
{

    protected $forms;

    protected function addForm($name, $form)
    {
        $this->forms[$name] = $form;
    }

    protected function getForm($name, $form)
    {
        return $this->forms[$name];
    }

    protected function getForms()
    {
        return $this->forms;
    }

    public function getModelForm(\Record $model, $action = null)
    {
        return $model->getForm($action);
    }

}