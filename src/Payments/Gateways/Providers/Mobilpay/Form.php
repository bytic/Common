<?php

namespace ByTIC\Common\Payments\Gateways\Providers\Mobilpay;

use ByTIC\Common\Payments\Gateways\Providers\AbstractGateway\Form as AbstractForm;
use Nip_File_System;

/**
 * Class Form
 * @package ByTIC\Common\Payments\Gateways\Providers\Mobilpay
 */
class Form extends AbstractForm
{

    protected $_files = [];

    public function initElements()
    {
        $this->addRadioGroup('sandbox', 'sandbox', true);
        $element = $this->getForm()->getElement('mobilpay[sandbox]');
        $element->getRenderer()->setSeparator('');
        $element->addOption('yes', 'Yes');
        $element->addOption('no', 'No');

        $this->addInput('signature', 'Signature', false);
    }

    public function getDataFromModel()
    {
        parent::getDataFromModel();
        $files = $this->getForm()->getModel()->findFiles();
        if (is_object($files['public.cer'])) {
            $this->addInput('file', 'Certificate', true);
            $element = $this->getForm()->getElement('mobilpay[file]');
            $element->setAttrib('readonly', 'readonly');
            $element->setValue('public.cer');

            $text = '<a href="' . $this->getForm()->getModel()->getDeteleFileURL(array('file' => 'public.cer')) . '">[Delete]</a>';
            $decorator = $element->newDecorator('text')->setText($text);
            $element->attachDecorator($decorator);
        } else {
            $this->addFile('file', 'Certificate', false);
        }

        if (is_object($files['private.key'])) {
            $this->addInput('private-key', 'Private key', true);
            $element = $this->getForm()->getElement('mobilpay[private-key]');
            $element->setAttrib('readonly', 'readonly');
            $element->setValue('private.key');

            $text = '<a href="' . $this->getForm()->getModel()->getDeteleFileURL(array('file' => 'private.key')) . '">[Delete]</a>';
            $decorator = $element->newDecorator('text')->setText($text);
            $element->attachDecorator($decorator);
        } else {
            $this->addFile('private-key', 'Private key', false);
        }
        $this->getForm()->getDisplayGroup($this->getGateway()->getLabel())
            ->addElement($this->getForm()->getElement('mobilpay[file]'));
        $this->getForm()->getDisplayGroup($this->getGateway()->getLabel())
            ->addElement($this->getForm()->getElement('mobilpay[private-key]'));

    }

    public function processValidation()
    {
        if ($_FILES['mobilpay']) {
            $publicData = $this->getForm()->getElement('mobilpay[file]')->getValue();
            if (is_array($publicData) && $publicData['tmp_name']) {
                $errorPublic = Nip_File_System::instance()->getUploadError(
                    $publicData,
                    $this->getFileModel('public.cer')->getExtensions());
                if ($errorPublic) {
                    $this->getForm()->getElement('mobilpay[file]')->addError($error);
                }
            }

            $privateData = $this->getForm()->getElement('mobilpay[private-key]')->getValue();
            if (is_array($privateData) && $privateData['tmp_name']) {
                $errorPrivate = Nip_File_System::instance()->getUploadError(
                    $privateData,
                    $this->getFileModel('private.key')->getExtensions());

                if ($errorPrivate) {
                    $this->getForm()->getElement('mobilpay[private-key]')->addError($error);
                }
            }
        }

        return true;
    }

    public function getFileModel($type)
    {
        if (!$this->_files[$type]) {
            $model = $this->getForm()->getModel();

            $this->_files[$type] = new Payment_Method_File_Mobilpay();
            $this->_files[$type]->setModel($model);
        }

        return $this->_files[$type];
    }

    public function process()
    {
        $fileData = $this->getForm()->getElement('mobilpay[file]')->getValue();

        if ($fileData) {
            $this->getFileModel('public.cer')->upload($fileData);
        }

        $fileData = $this->getForm()->getElement('mobilpay[private-key]')->getValue();

        if ($fileData) {
            $this->getFileModel('private.key')->upload($fileData);
        }

        return true;
    }
}