<?php

namespace ByTIC\Common\Records\Emails\Templates;

/**
 * Class Parser
 * @package ByTIC\Common\Records\Emails\Templates
 */
class Parser
{
    /**
     * @var array
     */
    protected $vars = [];

    /**
     * @param $content
     * @return string
     */
    public function parse($content)
    {
        $translate = [];
        foreach ($this->vars as $key => $value) {
            $key = '{{' . $key . '}}';
            $translate[$key] = $value;
        }

        $content = strtr($content, $translate);

        return $content;
    }

    /**
     * @param $vars
     * @return $this
     */
    public function setVars($vars)
    {
        $this->vars = $vars;
        return $this;
    }
}
