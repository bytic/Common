<?php

namespace ByTIC\Common\Records\Emails;

use ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait as AbstractRecordTrait;
use ByTIC\Common\Records\Traits\Media\Files\RecordTrait as FilesRecordTrait;
use ByTIC\Common\Records\Traits\Media\Generic\RecordTrait as MediaRecordTrait;
use Nip\Mail\Mailer;
use Nip\Mail\Message;
use Nip\Mail\Models\Mailable\RecordTrait as MailableRecordTrait;
use Nip\Records\Record;
use Nip_File_System;
use Swift_Attachment;

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
    use AbstractRecordTrait;

    use MediaRecordTrait;
    use FilesRecordTrait;
    use MailableRecordTrait;

    /**
     * @var array
     */
    protected $mergeTags = null;

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
        return $this->getBody();
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return array
     */
    public function getTos()
    {
        $emailsTos = [];
        if (preg_match_all('/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/', $this->to, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $m) {
                if (!empty($m[2])) {
                    $emailsTos[trim($m[2], '<>')] = $m[1];
                } else {
                    $emailsTos[$m[1]] = '';
                }
            }
        }

        return $emailsTos;
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

    /**
     * @return array
     */
    public function getFrom()
    {
        return [$this->from => $this->from_name];
    }

    /**
     * @param Message $message
     */
    public function buildMailMessageAttachments(&$message)
    {
        $emailFiles = $this->findFiles();
        foreach ($emailFiles as $emailFile) {
            $message->attach(Swift_Attachment::fromPath($emailFile->getPath()));
        }
    }

    /**
     * @param Mailer $mailer
     * @param Message $message
     * @param $response
     */
    protected function afterSend($mailer, $message, $response)
    {
        if ($response > 0) {
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
    }
}
