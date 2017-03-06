<?php

namespace ByTIC\Common\Sections;

class Section extends \Nip\Records\AbstractModels\Record
{

    public function getName()
    {
        return $this->name;
    }

    public function getSubdomain()
    {
        return $this->subdomain;
    }

    public function getURL($url = false)
    {
        $url = $url ? $url : BASE_URL;
        return str_replace('://' . \Nip\Request::instance()->getHttp()->getSubdomain() . '.42km', '://' . $this->subdomain . '.42km', $url);
    }

    public function getPath($path = false)
    {
        $curentSubdomain = \Nip\Request::instance()->getHttp()->getSubdomain();
        if (\Nip\Request::instance()->getHttp()->getSubdomain() == 'www') {
            $path = str_replace(ROOT_PATH, ROOT_PATH . $this->subdomain . DS, $path);
        } elseif ($this->subdomain == 'www') {
            $path = str_replace($curentSubdomain . DS, '', $path);
        }
        return $path;
    }


    public function isCurrent()
    {
        return $this->subdomain == \Nip\Request::instance()->getHttp()->getSubdomain();
    }


    public function forOrganizers()
    {
        return $this->organizers === true;
    }

    public function isMenu()
    {
        return $this->menu === true;
    }

}