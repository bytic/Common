<?php

namespace ByTIC\Common\Controllers\Traits\Async;

trait Files
{
    public function uploadAttachment()
    {
        $item = $this->checkItem();

        $file = $item->uploadFile($_FILES['Filedata']);

        if ($file) {
            $this->_response['type'] = 'success';
            $this->_response['url'] = $file->getURL();
            $this->_response['name'] = $file->getName();
            $this->_response['extension'] = $file->getExtension();
            $this->_response['size'] = $file->formatSize();
            $this->_response['time'] = date("d.m.Y H:i", $file->getTime());
        } else {
            $this->_response['type'] = 'error';
        }
    }


    public function removeFile()
    {
        $item = $this->checkItem();

        if ($item->removeFile($_POST)) {
            $this->_response['type'] = 'success';
        }
    }

}