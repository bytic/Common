<?php

namespace ByTIC\Common\Records\Emails;

use ByTIC\Common\Records\Emails\Templates\Parser;
use Nip\Records\Record;
use Nip_Config;
use Nip_File_System;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Emails
 *
 * @property int $id_item
 * @property string $type
 * @property string $from
 * @property string $from_name
 * @property string $smtp_host
 * @property string $smtp_user
 * @property string $smtp_password
 * @property string $to
 * @property string $subject
 * @property string $compiled_subject
 * @property string $body
 * @property string $compiled_body
 * @property string $vars
 * @property string $is_html
 * @property string $sent
 * @property string $date_sent
 * @property string $created
 */
trait EmailTrait
{
    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    use \ByTIC\Common\Records\Traits\Media\Generic\RecordTrait;
    use \ByTIC\Common\Records\Traits\Media\Files\RecordTrait;
    use \Nip\Mail\Models\Mailable\RecordTrait;

    /**
     * @var array
     */
    protected $mergeTags = null;

    protected $_parser;

    public function send()
    {
        $send = parent::send();

        if ($send) {
            $this->afterSend();
        }
    }

    protected function afterSend()
    {
        $this->sent = 'yes';
        $this->smtp_user = '';
        $this->smtp_host = '';
        $this->smtp_password = '';
        $this->subject = '';
        $this->body = '';
//        $this->vars = '';
        $this->date_sent = date(DATE_DB);
        $this->update();

        $attachments = $this->findFiles();
        if (count($attachments) > 0) {
            $attachment = reset($attachments);
            Nip_File_System::instance()->removeDirectory($attachment->getDirPath());
        }
    }

    public function populateFromConfig()
    {
        $config = app('config');
        $this->from = $config->get('EMAIL.from');
        $this->from_name = $config->get('EMAIL.from_name');

        $this->smtp_host = $config->get('SMTP.host');
        $this->smtp_user = $config->get('SMTP.username');
        $this->smtp_password = $config->get('SMTP.password');
    }

    /**
     * @param Record $item
     */
    public function populateFromItem($item)
    {
        $this->id_item = $item->id;
        $this->type = inflector()->singularize($item->getManager()->getTable());
    }

    /**
     * @return string
     */
    public function getPreviewBody()
    {
        return $this->getParser()->parse($this->getBody());
    }

    public function getParser()
    {
        if (!$this->_parser) {
            $this->initParser();
        }

        return $this->_parser;
    }

    /**
     * @param mixed $parser
     */
    public function setParser($parser)
    {
        $this->_parser = $parser;
    }

    public function initParser()
    {
        $this->setParser($this->newParser());
    }

    /**
     * @return Parser
     */
    public function newParser()
    {
        $class = $this->getParserClass();
        /** @var Parser $parser */
        $parser = new $class();
        $parser->setVars($this->getVars());

        return $parser;
    }

    /**
     * @return string
     */
    public function getParserClass()
    {
        return '\ByTIC\Common\Records\Emails\Templates\Parser';
    }

    /**
     * @return array
     */
    public function getVars()
    {
        return $this->getMergeTags();
    }

    /**
     * @return array|null
     */
    public function getMergeTags()
    {
        if ($this->mergeTags === null) {
            $this->initMergeTags();
        }

        return $this->mergeTags;
    }

    /**
     * @param array $mergeTags
     */
    public function setMergeTags($mergeTags)
    {
        $this->mergeTags = $mergeTags;
    }

    protected function initMergeTags()
    {
        $mergeTags = unserialize($this->vars);
        $this->setMergeTags($mergeTags);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        $return = $this->sent == 'yes' ? $this->compiled_body : $this->body;

        return $return;
    }

    /**
     * @param $vars
     * @return $this
     */
    public function setVars($vars)
    {
        $this->mergeTags = $vars;

        return $this;
    }

    /**
     * @return mixed
     */
    public function insert()
    {
        $this->vars = serialize($this->mergeTags);
        $this->created = date(DATE_DB);

        return parent::insert();
    }

    public function compileBody()
    {
        $header = '';
        $footer = '';
        $this->body = $header.$this->body.$footer;
    }

    /**
     * @return mixed
     */
    public function getActivitiesByEmail()
    {
        if (!$this->getRegistry()->exists('activities-email')) {
            $actEmail = [];
            $activities = $this->getActivities();
            foreach ($activities as $activity) {
                $actEmail[$activity->email][] = $activity;
            }
            $this->getRegistry()->set('activities-email', $actEmail);
        }

        return $this->getRegistry()->get('activities-email');
    }

    /**
     * @return mixed
     */
    public function getLinksByEmail()
    {
        if (!$this->getRegistry()->exists('links-email')) {
            $linksEmail = [];
            $links = $this->getLinks();
            foreach ($links as $link) {
                $actEmail[$link->url][$link->email] = $link;
            }
            $this->getRegistry()->set('links-email', $actEmail);
        }

        return $this->getRegistry()->get('links-email');
    }

    public function delete()
    {
        $file = $this->getNewFile();
        $attachmentPath = $file->getDirPath();
        Nip_File_System::instance()->removeDirectory($attachmentPath);

        return parent::delete();
    }

    /**
     * @param null $value
     * @return bool
     */
    public function IsHTML($value = null)
    {
        if (is_bool($value)) {
            $this->is_html = $value ? 'yes' : 'no';
        }

        return $this->is_html == 'yes';
    }

    public function parse()
    {
        $this->compiled_subject = $this->getParser()->parse($this->subject);
        $this->compiled_body = $this->getParser()->parse($this->body);
    }
}
