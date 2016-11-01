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
                $this->response['type'] = 'success';
                $this->response['url'] = $image->getUrl();
                $this->response['path'] = $image->getPath();
                $this->response['imageType'] = $image->getImageType();
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
            $this->response['message'] = 'Logo sters';
        } else {
            $this->response['message'] = 'Logo-ul convertit la default';
        }

        $image = $item->getLogo($_REQUEST['type']);
        $this->response['type'] = 'success';
        $this->response['url'] = $image->getUrl();
        $this->response['path'] = $image->getPath();
    }
}
