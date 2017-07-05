<?php

namespace ByTIC\Common\Controllers\Traits\Async;

/**
 * Class Files
 * @package ByTIC\Common\Controllers\Traits\Async
 */
trait Files
{
    public function uploadAttachment()
    {
        $item = $this->checkItem();

        $file = $item->uploadFile($_FILES['Filedata']);

        if ($file) {
            $response['type'] = 'success';
            $response['url'] = $file->getURL();
            $response['name'] = $file->getName();
            $response['extension'] = $file->getExtension();
            $response['size'] = $file->formatSize();
            $response['time'] = date("d.m.Y H:i", $file->getTime());
        } else {
            $response['type'] = 'error';
        }
        $this->setResponseValues($response);
    }

    /**
     * @param $values
     * @return void
     */
    abstract public function setResponseValues($values);

    public function removeFile()
    {
        $item = $this->checkItem();

        if ($item->removeFile($_POST)) {
            $response['type'] = 'success';
        }
        $this->setResponseValues($response);
    }
}