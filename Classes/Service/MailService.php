<?php
namespace NeosRulez\Neos\Essentials\Service;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\SwiftMailer\Message;

class MailService extends AbstractService
{

    /**
     * @Flow\InjectConfiguration(package="NeosRulez.Neos.Essentials", path="mail")
     * @var array
     */
    protected $mailSettings;

    /**
     * @param string $packageName
     * @param string $fusionPathAndFileName
     * @param array $variables
     * @param string $subject
     * @param string $sender
     * @param string $recipient
     * @param string|bool $replyTo
     * @param string|bool $cc
     * @param string|bool $bcc
     * @param array $attachments
     * @return void
     */
    public function sendMail(string $packageName, string $fusionPathAndFileName, array $variables, string $subject, string $sender, string $recipient, string|bool $replyTo = false, string|bool $cc = false, string|bool $bcc = false, array $attachments = []): void
    {
        $fusionView = new FusionView();
        $fusionView->setPackageKey($packageName);
        $fusionView->setFusionPath($fusionPathAndFileName);
        $fusionView->assignMultiple($variables);

        $mail = new Message();
        $mail
            ->setFrom($sender)
            ->setTo([$recipient => $recipient])
            ->setSubject($subject);
        $mail->setBody($fusionView->render(), 'text/html');

        if($replyTo) {
            $mail->setReplyTo([$replyTo => $replyTo]);
        }

        if($cc) {
            $mail->setCc([$cc => $cc]);
        }

        if($bcc) {
            $mail->setBcc([$bcc => $bcc]);
        }

        if(!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $attachment = \Swift_Attachment::fromPath($attachment);
                $mail->attach($attachment);
            }
        }

        $mail->send();
    }

}
