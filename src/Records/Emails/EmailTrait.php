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

    /**
     * @var array
     */
    protected $_vars = [];

    protected $_parser;

    /**
     * @param bool $data
     * @return mixed
     */
    public function writeData($data = false)
    {
        if (isset($data['vars']) && is_string($data['vars'])) {
            $data['vars'] = unserialize($data['vars']);
            $this->_vars = $data['vars'];
        }

        return parent::writeData($data);
    }

    public function populateFromConfig()
    {
        $this->from = Nip_Config::instance()->EMAIL->from;
        $this->from_name = Nip_Config::instance()->EMAIL->from_name;

        $this->smtp_host = Nip_Config::instance()->SMTP->host;
        $this->smtp_user = Nip_Config::instance()->SMTP->username;
        $this->smtp_password = Nip_Config::instance()->SMTP->password;
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
        return $this->_vars;
    }

    /**
     * @param $vars
     * @return $this
     */
    public function setVars($vars)
    {
        $this->_vars = $vars;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        $return = $this->sent == 'yes' ? $this->compiled_body : $this->body;
        return $return;
    }

    public function insert()
    {
        $this->vars = serialize($this->_vars);
        $this->created = date(DATE_DB);
        return parent::insert();
    }

    /**
     * @return bool|string
     */
    public function send()
    {
        if ($this->from && $this->to) {
            $return = $this->_sendSendGrid();

            if ($return === true) {
                $this->sent = 'yes';
//                $this->smtp_user = '';
//                $this->smtp_host = '';
                $this->smtp_password = '';
                $this->subject = '';
                $this->body = '';
//                $this->vars = '';
                $this->date_sent = date(DATE_DB);
                $this->update();

                $attachments = $this->findFiles();
                if (count($attachments) > 0) {
                    $attachment = reset($attachments);
                    Nip_File_System::instance()->removeDirectory($attachment->getDirPath());
                }
            }
            return $return;
        }
        return false;
    }

    /**
     * @return bool|string
     */
    private function _sendSendGrid()
    {
        $this->compileBody();
        //$this->parse();

        $this->compiled_subject = $this->subject;
        $this->compiled_body = $this->body;

        $mail = new \SendGrid\Mail();
        $mail->addCategory($this->type);
        $mail->addCustomArg("id_email", (string)$this->id);

        $email = new \SendGrid\Email($this->from_name, $this->from);
        $mail->setFrom($email);

        $reply_to = new \SendGrid\ReplyTo($this->from);
        $mail->setReplyTo($reply_to);

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

        $i = 0;
        foreach ($emailsTos as $emailTo => $nameTo) {
            $personalization = new \SendGrid\Personalization();

            $email = new \SendGrid\Email($nameTo, $emailTo);
            $personalization->addTo($email);

            $personalization->setSubject($this->compiled_subject);

            $vars = $this->getVars();
            foreach ($vars as $varKey => $value) {
                if (is_array($value)) {
                    $value = $value[$i];
                }
                $value = (string)$value;
                $personalization->addSubstitution('{{' . $varKey . '}}', $value);
            }

            $mail->addPersonalization($personalization);

            $i = 1;
        }

        $html = new \Html2Text\Html2Text($this->compiled_body);
        $content = new \SendGrid\Content("text/plain", $html->getText());
        $mail->addContent($content);

        $content = new \SendGrid\Content("text/html", $this->compiled_body);
        $mail->addContent($content);

        $emailFiles = $this->findFiles();
        foreach ($emailFiles as $emailFile) {
            /** @var \Email_File $emailFile */
            $attachment = new \SendGrid\Attachment();
            $attachment->setContent(base64_encode(file_get_contents($emailFile->getPath())));
            $attachment->setType($emailFile->getContentType());
            $attachment->setFilename($emailFile->getName());
            $attachment->setDisposition("attachment");
            $attachment->setContentId('Attachment');
            $mail->addAttachment($attachment);
        }

        $sg = new \SendGrid("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");

        $response = $sg->client->mail()->send()->post($mail);

        if ($response->statusCode() == '202') {
            return true;
        } else {
//            echo $response->statusCode();
//            echo '-----------';
//            echo $response->body();
//            echo '-----------';
//            echo $response->headers();
//            die('----------');
            return $response->body() . $response->headers();
        }
    }

    public function compileBody()
    {
        $header = '';
        $footer = '';
        $this->body = $header . $this->body . $footer;
    }

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

    private function _sendSMTP()
    {
        $this->_mail = new Nip_Mailer();
//			$this->_mail->debugSMTP();

        $this->_mail->setFrom($this->from, $this->from_name);

        $to = explode(',', $this->to);
        foreach ($to as $email) {
            $this->_mail->addTo($email);
        }

        if ($this->cc) {
            $this->_mail->AddCC($this->cc);
        }

        if ($this->smtp_host) {
            if ($this->smtp_user && $this->smtp_password) {
                $this->_mail->authSMTP($this->smtp_host, $this->smtp_user, $this->smtp_password);
            } else {
                $this->_mail->setSMTP($this->smtp_host);
            }
        }

        $this->_mail->IsHTML($this->IsHTML());

        $this->compileBody();
        $this->parse();

        $this->_mail->setSubject($this->compiled_subject);

        $this->_mail->setBody($this->compiled_body);
        $this->_mail->setAltBody($this->compiled_body);

        $attachments = $this->findFiles();

        foreach ($attachments as $attachment) {
            $this->_mail->addAttachment($attachment->getPath());
        }

        return $this->_mail->send();
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
