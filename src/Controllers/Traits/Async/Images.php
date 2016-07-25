<?php

namespace ByTIC\Common\Controllers\Traits\Async;

trait Images
{

    public function uploadImage()
    {
        $item = $this->checkItem();

        $image = $item->uploadImage();

        if (is_object($image)) {
            $this->_response['type'] = 'success';
            $this->_response['url'] = $image->url;
            $this->_response['path'] = $image->name;
            $this->_response['width'] = $item->getImageWidth("default");
            $this->_response['height'] = $item->getImageHeight("default");
        }

        $this->_response['message'] = $item->errors['upload'];
    }


    public function cropImage()
    {
        $item = $this->checkItem($_POST);

        $image = $item->cropImages($_POST);

        if ($image) {
            $this->_response['type'] = 'success';
            $this->_response['url'] = $image->url;
            $this->_response['name'] = $image->name;
        }
    }


    public function setDefaultImage()
    {
        $item = $this->checkItem();

        if ($item->setDefaultImage($_POST)) {
            $this->_response['type'] = 'success';
        }
    }


    public function removeImage()
    {
        $item = $this->checkItem();

        if ($item->removeImage($_POST)) {
            $this->_response['type'] = 'success';
        }
    }

    


}