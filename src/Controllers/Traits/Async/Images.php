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
            $response['type'] = 'success';
            $response['url'] = $image->url;
            $response['path'] = $image->name;
            $response['width'] = $item->getImageWidth("default");
            $response['height'] = $item->getImageHeight("default");
        }

        $response['message'] = $item->errors['upload'];
        $this->setResponseValues($response);
    }

    /**
     * @param $values
     * @return void
     */
    abstract public function setResponseValues($values);

    public function cropImage()
    {
        $item = $this->checkItem($_POST);

        $image = $item->cropImages($_POST);

        if ($image) {
            $response['type'] = 'success';
            $response['url'] = $image->url;
            $response['name'] = $image->name;
        }
        $this->setResponseValues($response);
    }

    public function setDefaultImage()
    {
        $item = $this->checkItem();

        if ($item->setDefaultImage($_POST)) {
            $item->update();
            $response['type'] = 'success';
        }
    }

    public function removeImage()
    {
        $item = $this->checkItem();

        if ($item->removeImage($_POST)) {
            $response['type'] = 'success';
        }
        $this->setResponseValues($response);
    }
}
