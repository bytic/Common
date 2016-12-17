<?php

namespace ByTIC\Common\Application\Modules\Admin\Controllers\Traits;

use ByTIC\Common\Controllers\Traits\Async\ResponseTrait;

/**
 * Class AbstractAsyncControllerTrait
 * @package ByTIC\Common\Application\Modules\Admin\Controllers\Traits
 */
trait AbstractAsyncControllerTrait
{
    use ResponseTrait;

    public function uploadImage()
    {
        $item = $this->checkItem();

        $image = $item->uploadImage();

        if (is_object($image)) {
            $this->response_values['type'] = 'success';
            $this->response_values['url'] = $image->url;
            $this->response_values['path'] = $image->name;
            $this->response_values['width'] = $item->getImageWidth("default");
            $this->response_values['height'] = $item->getImageHeight("default");
        }

        $this->response_values['message'] = $item->errors['upload'];
    }


    public function cropImage()
    {
        $item = $this->checkItem($_POST);

        $image = $item->cropImages($_POST);

        if ($image) {
            $this->response_values['type'] = 'success';
            $this->response_values['url'] = $image->url;
            $this->response_values['name'] = $image->name;
        }
    }


    public function setDefaultImage()
    {
        $item = $this->checkItem();

        if ($item->setDefaultImage($_POST)) {
            $this->response_values['type'] = 'success';
        }
    }


    public function removeImage()
    {
        $item = $this->checkItem();

        if ($item->removeImage($_POST)) {
            $this->response_values['type'] = 'success';
        }
    }

    public function uploadLogo()
    {
        $item = $this->checkItem();

        $type = $_REQUEST['type'];
        if (in_array($type, $item->logoTypes)) {
            $image = $item->uploadLogo($type);

            if (is_object($image)) {
                $response['url'] = $image->getUrl();
                $response['path'] = $image->getPath();
                $response['imageType'] = $image->getImageType();
                $this->sendSuccess('Upload cu success', $response);
            } else {
                $this->sendError($item->errors['upload']);
            }
        } else {
            $this->sendError('bad logo type');
        }
    }

    public function removeLogo()
    {
        $item = $this->checkItem();

        if ($item->removeLogo($_REQUEST)) {
            $message = 'Logo sters';
        } else {
            $message = 'Logo-ul convertit la default';
        }

        $image = $item->getLogo($_REQUEST['type']);
        $response['type'] = 'success';
        $response['url'] = $image->getUrl();
        $response['path'] = $image->getPath();
        $this->sendSuccess($message, $response);
    }

    public function uploadAttachment()
    {
        $item = $this->checkItem();

        $file = $item->uploadFile($_FILES['Filedata']);

        if ($file) {
            $this->response_values['type'] = 'success';
            $this->response_values['url'] = $file->getURL();
            $this->response_values['name'] = $file->getName();
            $this->response_values['extension'] = $file->getExtension();
            $this->response_values['size'] = $file->formatSize();
            $this->response_values['time'] = date("d.m.Y H:i", $file->getTime());
        } else {
            $this->response_values['type'] = 'error';
        }
    }

    public function removeFile()
    {
        $item = $this->checkItem();

        if ($item->removeFile($_POST)) {
            $this->sendSuccess('success');
        }
    }

    public function order()
    {
        $name = $this->getRequest()->name;
        if ($name && is_array($this->getRequest()->$name)) {
            $items = $this->getManager()->getAll();
            $ids = $this->getRequest()->$name;
            foreach ($ids as $key => $idItem) {
                $item = $items[$idItem];
                $item->pos = $key + 1;
                $item->update();
            }
            $this->sendSuccess('Saved');
        }
    }

    protected function afterAction()
    {
        die();
    }
}
