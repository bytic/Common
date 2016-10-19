<?php

namespace ByTIC\Common\Records\Emails\Builder;

use ByTIC\Common\Records\Emails\EmailsTrait;
use ByTIC\Common\Records\Emails\EmailTrait;
use Nip\Records\Record;

/**
 * Class AbstractBuilder
 * @package ByTIC\Common\Records\Emails\Builder
 */
abstract class AbstractBuilder
{
    protected $item = null;

    /**
     * @var EmailTrait
     */
    protected $email = null;

    protected $mergeTags = [];


    /**
     * @return bool
     */
    public function save()
    {
        if ($this->isActive()) {
            $this->createEmail();

            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * @return EmailTrait
     */
    public function createEmail()
    {
        $this->compile();
        $this->compileSubject();
        $this->compileEmailBody();
        $this->saveEmail();
        $this->compileAttachments();

        return $this->getEmail();
    }

    /**
     * @return $this
     */
    protected function compile()
    {
        return $this;
    }

    protected function compileSubject()
    {
        $this->getEmail()->subject = $this->generateEmailSubject();
    }

    /**
     * @return EmailTrait
     */
    public function getEmail()
    {
        if ($this->email === null) {
            $this->initEmail();
        }

        return $this->email;
    }

    /**
     * @param EmailTrait $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    protected function initEmail()
    {
        $this->setEmail($this->generateNewEmail());
    }

    /**
     * @return EmailTrait
     */
    protected function generateNewEmail()
    {
        $email = $this->newBlankEmail();
        return $this->hydrateEmail($email);
    }

    /**
     * @return EmailTrait
     */
    protected function newBlankEmail()
    {
        return $this->getEmailsManager()->getNew();
    }

    /**
     * @return EmailsTrait
     */
    abstract protected function getEmailsManager();

    /**
     * @param EmailTrait $email
     * @return mixed
     */
    protected function hydrateEmail($email)
    {
        $email->IsHTML('yes');
        $email->populateFromConfig();
        if ($this->hasItem()) {
            $email->populateFromItem($this->getItem());
        }
        return $email;
    }

    /**
     * @return bool
     */
    public function hasItem()
    {
        return $this->item instanceof \Record;
    }

    /**
     * @return \Record
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Record|BuilderAwareTrait $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @return string
     */
    abstract protected function generateEmailSubject();

    protected function compileEmailBody()
    {
        $this->getEmail()->body = $this->generateEmailBody();

//        ob_end_clean();
//        echo $this->getEmail()->body;
//        die();
    }

    /**
     * @return null|string
     */
    abstract protected function generateEmailBody();

    public function saveEmail()
    {
        $this->getEmail()
            ->setVars($this->getMergeTags())
            ->saveRecord();
    }

    /**
     * @return array
     */
    public function getMergeTags()
    {
        return $this->mergeTags;
    }

    public function compileAttachments()
    {
    }

    /**
     * @param $name
     * @param $value
     * @return AbstractBuilder
     */
    public function setMergeTag($name, $value)
    {
        $this->mergeTags[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getMergeTag($name)
    {
        return $this->mergeTags[$name];
    }
}
