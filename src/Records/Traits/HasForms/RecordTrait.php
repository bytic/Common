<?php

namespace ByTIC\Common\Records\Traits\HasForms;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait as AbstractTrait;
use Nip_Form_Model as Form;

/**
 * Class RecordTrait
 *
 * @package ByTIC\Common\Records\Traits\HasForms
 *
 * @method RecordsTrait getManager
 */
trait RecordTrait
{
    use AbstractTrait;

    protected $forms = [];

    /**
     * Get Form object by name
     *
     * @param string $type Form name
     *
     * @return Form
     */
    public function getForm($type = null)
    {
        if (!isset($this->forms[$type])) {
            $form = $this->getManager()->newForm($type);

            $this->forms[$type] = $this->initForm($form);
        }

        return $this->forms[$type];
    }

    /**
     * Init a form object for this model
     *
     * @param Form $form Form object
     *
     * @return Form
     */
    public function initForm($form)
    {
        /** @noinspection PhpParamsInspection */
        $form->setModel($this);

        return $form;
    }
}
