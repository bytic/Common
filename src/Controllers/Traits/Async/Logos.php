<?php

namespace ByTIC\Common\Controllers\Traits\Async;

/**
 * Class Logos
 * @package ByTIC\Common\Controllers\Traits\Async
 */
trait Logos
{
    public function uploadLogo()
    {
        $item = $this->checkItem();

        $type = $_REQUEST['type'];
        if (in_array($type, $item->logoTypes)) {
            $image = $item->uploadLogo($type);

            if (is_object($image)) {
                $response['type'] = 'success';
                $response['url'] = $image->getUrl();
                $response['path'] = $image->getPath();
                $response['imageType'] = $image->getImageType();
                $this->setResponseValues($response);
            } else {
                $this->sendError($item->errors['upload']);
            }
        } else {
            $this->sendError('bad logo type');
        }
    }

    /**
     * @param $values
     * @return void
     */
    abstract public function setResponseValues($values);

    public function removeLogo()
    {
        $item = $this->checkItem();

        if ($item->removeLogo($_REQUEST)) {
            $response['message'] = 'Logo sters';
        } else {
            $response['message'] = 'Logo-ul convertit la default';
        }

        $image = $item->getLogo($_REQUEST['type']);
        $response['type'] = 'success';
        $response['url'] = $image->getUrl();
        $response['path'] = $image->getPath();
        $this->setResponseValues($response);
    }
}
