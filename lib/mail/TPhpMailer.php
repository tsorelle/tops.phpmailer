<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/1/2017
 * Time: 9:57 AM
 */

namespace Tops\mail;

use PHPMailer\PHPMailer\PHPMailer;
use Tops\sys\TConfiguration;
use Tops\sys\TPath;
use Tops\sys\TWebSite;

/**
 * Class TPhpMailer
 * @package Tops\mail
 *
 */
class TPhpMailer implements IMailer
{
    private $enabled = true;

    /**
     * @var PHPMailer
     */
    private $mailer;
    private $initialized = false;
    private $basedir;
    
    public function __construct()
    {
        $senderType = TConfiguration::getValue('sendmail','mail',1);
        $baseDir = TConfiguration::getValue('basedir','mail');
        if ($baseDir !== false) {
            $this->baseDir = TWebSite::ExpandUrl($baseDir);
        }

        $this->mailer = new PHPMailer();
        if (empty($senderType)) {
            $this->enabled = false;
        }
        else {
            switch ($senderType) {
                case 'smtp' :
                    $this->configureSmtp();
                    break;
                // might support additional mailer types later
                default:
                    $this->mailer->isMail();
                    break;
            }
        }

    }

    public function configureSmtp()
    {
        $this->mailer->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mailer->SMTPDebug = TConfiguration::getValue('debug', 'mail', 0);

        //Set the hostname of the mail server
        $this->mailer->Host = TConfiguration::getValue('host', 'mail', 'localhost');
        //Set the SMTP port number - likely to be 25, 465 or 587
        $this->mailer->Port = TConfiguration::getValue('port', 'mail', 25);
        //Whether to use SMTP authentication
        $auth = TConfiguration::getBoolean('auth', 'mail');
        if ($auth) {
            $this->mailer->SMTPAuth = true;
            //Username to use for SMTP authentication
            $this->mailer->Username = TConfiguration::getValue('username', 'mail', '');
            //Password to use for SMTP authentication
            $this->mailer->Password = TConfiguration::getValue('password', 'mail', '');
        }
    }

    /**
     * @param TEMailMessage $message
     * @return bool|string
     */
    public function send(TEMailMessage $message)
    {
        if (!$this->enabled) {
            return true;
        }
        $address = $message->getFromAddress();
        $this->mailer->setFrom($address->getAddress(), $address->getName());
        $address = $message->getReplyTo();
        $this->mailer->addReplyTo($address->getAddress(), $address->getName());
        $this->mailer->Subject = $message->getSubject();

        foreach ($message->getRecipients() as $recipient) {
            $this->mailer->addAddress($recipient->getAddress(), $recipient->getName());
        }

        foreach ($message->getCCs() as $recipient) {
            $this->mailer->addCC($recipient->getAddress(), $recipient->getName());
        }

        foreach ($message->getBCCs() as $recipient) {
            $this->mailer->addBCC($recipient->getAddress(), $recipient->getName());
        }

        $contentType = $message->getContentType();
        $isHtml = $contentType != TContentType::Text;

        $this->mailer->isHTML($isHtml);
        if ($isHtml) {
            $this->mailer->msgHTML($message->getMessageBody(), $this->basedir);
            if ($contentType == TContentType::MultiPart) {
                $this->mailer->AltBody = $message->getTextPart();
            }
        } else {
            $this->mailer->Body = $message->getMessageBody();
        }

        $attachments = $message->getAttachments();

        foreach ($attachments as $attachment) {
            $path = TPath::fromFileRoot($attachment);
            if ($path===false) {
                return "Attachment path not found: $attachment";
            }
            $this->mailer->addAttachment($path);
        }

        if (!$this->mailer->send()) {
            return $this->mailer->ErrorInfo;
        } else {
            return true;
        }
    }

    public function setSendEnabled($value)
    {
        $this->enabled = false;
    }
}