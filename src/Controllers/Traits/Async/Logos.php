<?php

namespace ByTIC\Common\Controllers\Traits\Async;

trait Logos
{

    public function uploadLogo()
    {
        $item = $this->checkItem();

        $type = $_REQUEST['type'];
        if (in_array($type, $item->logoTypes)) {
            $image = $item->uploadLogo($type);

            if (is_object($image)) {
                $this->_response['type'] = 'success';
                $this->_response['url'] = $image->getUrl();
                $this->_response['path'] = $image->getPath();
                $this->_response['imageType'] = $image->getImageType();
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
            $this->_response['message'] = 'Logo sters';
        } else {
            $this->_response['message'] = 'Logo-ul convertit la default';
        }

        $image = $item->getLogo($_REQUEST['type']);
        $this->_response['type'] = 'success';
        $this->_response['url'] = $image->getUrl();
        $this->_response['path'] = $image->getPath();
    }

}