<?php

namespace ByTIC\Common\Controllers\Traits\Async;

trait Covers
{
    
    public function uploadCover()
    {
        $item = $this->checkItem();

        $cover = $item->uploadCover();

        if (is_object($cover)) {
            $this->_response['type'] = 'success';
            $this->_response['url'] = $cover->url;
            $this->_response['path'] = $cover->name;
            $this->_response['width'] = $cover->cropWidth;
            $this->_response['height'] = $cover->cropHeight;
        }

        $this->_response['message'] = $item->errors['upload'];
    }


    public function cropCover()
    {
        $item = $this->checkItem($_POST);

        $cover = $item->cropCovers($_POST);

        if ($cover) {
            $this->_response['type'] = 'success';
            $this->_response['url'] = $cover->url;
            $this->_response['name'] = $cover->name;
        }
    }

    public function setDefaultCover()
    {
        $item = $this->checkItem();

        if ($item->setDefaultCover($_POST)) {
            $this->_response['type'] = 'success';
        }
    }


    public function removeCover()
    {
        $item = $this->checkItem();

        if ($item->removeCover($_POST)) {
            $this->_response['type'] = 'success';
        }
    }

}