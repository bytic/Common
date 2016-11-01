<?php

namespace ByTIC\Common\Controllers\Traits\Async;

/**
 * Class Covers
 * @package ByTIC\Common\Controllers\Traits\Async
 */
trait Covers
{
    
    public function uploadCover()
    {
        $item = $this->checkItem();

        $cover = $item->uploadCover();

        if (is_object($cover)) {
            $this->response['type'] = 'success';
            $this->response['url'] = $cover->url;
            $this->response['path'] = $cover->name;
            $this->response['width'] = $cover->cropWidth;
            $this->response['height'] = $cover->cropHeight;
        }

        $this->response['message'] = $item->errors['upload'];
    }


    public function cropCover()
    {
        $item = $this->checkItem($_POST);

        $cover = $item->cropCovers($_POST);

        if ($cover) {
            $this->response['type'] = 'success';
            $this->response['url'] = $cover->url;
            $this->response['name'] = $cover->name;
        }
    }

    public function setDefaultCover()
    {
        $item = $this->checkItem();

        if ($item->setDefaultCover($_POST)) {
            $this->response['type'] = 'success';
        }
    }


    public function removeCover()
    {
        $item = $this->checkItem();

        if ($item->removeCover($_POST)) {
            $this->response['type'] = 'success';
        }
    }
}
