<?php

namespace ByTIC\Common\Controllers\Traits\Async;

/**
 * Class Images
 * @package ByTIC\Common\Controllers\Traits\Async
 */
trait Images
{

    public function uploadImage()
    {
        $item = $this->checkItem();

        $image = $item->uploadImage();

        if (is_object($image)) {
            $this->response['type'] = 'success';
            $this->response['url'] = $image->url;
            $this->response['path'] = $image->name;
            $this->response['width'] = $item->getImageWidth("default");
            $this->response['height'] = $item->getImageHeight("default");
        }

        $this->response['message'] = $item->errors['upload'];
    }

    public function cropImage()
    {
        $item = $this->checkItem($_POST);

        $image = $item->cropImages($_POST);

        if ($image) {
            $this->response['type'] = 'success';
            $this->response['url'] = $image->url;
            $this->response['name'] = $image->name;
        }
    }

    public function setDefaultImage()
    {
        $item = $this->checkItem();

        if ($item->setDefaultImage($_POST)) {
            $this->response['type'] = 'success';
        }
    }

    public function removeImage()
    {
        $item = $this->checkItem();

        if ($item->removeImage($_POST)) {
            $this->response['type'] = 'success';
        }
    }
}
