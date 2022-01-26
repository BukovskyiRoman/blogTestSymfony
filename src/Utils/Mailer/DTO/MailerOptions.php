<?php

namespace App\Utils\Mailer\DTO;

class MailerOptions
{
    private $recipient;

    /**
     * @return mixed
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param mixed $recipient
     */
    public function setRecipient($recipient): MailerOptions
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @param mixed $cc
     */
    public function setCc($cc): MailerOptions
    {
        $this->cc = $cc;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject): MailerOptions
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHtmlTemplate()
    {
        return $this->htmlTemplate;
    }

    /**
     * @param mixed $htmlTemplate
     */
    public function setHtmlTemplate($htmlTemplate): MailerOptions
    {
        $this->htmlTemplate = $htmlTemplate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext($context): MailerOptions
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text): MailerOptions
    {
        $this->text = $text;
        return $this;
    }

    private $cc;

    private $subject;

    private $htmlTemplate;

    private $context;

    private $text;
}