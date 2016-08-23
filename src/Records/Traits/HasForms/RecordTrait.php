<?php

namespace ByTIC\Common\Records\Traits\HasForms;
use Nip_Form_Model as Form;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\HasForms
 *
 * @method RecordsTrait getManager
 */
trait RecordTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    protected $_forms = array();

    /**
     * @param string $type
     * @return \Nip_Form
     */
    public function getForm($type = NULL)
    {
        if (!$this->_forms[$type]) {
            $form = $this->getManager()->newForm($type);

            $this->_forms[$type] = $this->initForm($form);
        }

        return $this->_forms[$type];
    }

    /**
     * @param Form $form
     * @return mixed
     */
    public function initForm($form)
    {
        /** @noinspection PhpParamsInspection */
        $form->setModel($this);
        return $form;
    }

}