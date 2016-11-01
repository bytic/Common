<?php

namespace ByTIC\Common\Controllers\Traits\Async;

trait Files
{
    public function uploadAttachment()
    {
        $item = $this->checkItem();

        $file = $item->uploadFile($_FILES['Filedata']);

        if ($file) {
            $this->response['type'] = 'success';
            $this->response['url'] = $file->getURL();
            $this->response['name'] = $file->getName();
            $this->response['extension'] = $file->getExtension();
            $this->response['size'] = $file->formatSize();
            $this->response['time'] = date("d.m.Y H:i", $file->getTime());
        } else {
            $this->response['type'] = 'error';
        }
    }


    public function removeFile()
    {
        $item = $this->checkItem();

        if ($item->removeFile($_POST)) {
            $this->response['type'] = 'success';
        }
    }

}