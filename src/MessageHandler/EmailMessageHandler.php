<?php

namespace App\MessageHandler;

use App\Message\MyEmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Email;

class EmailMessageHandler implements MessageHandlerInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(MyEmailMessage $message)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($message->getEmailTo())
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>You have a new comment</p>');

        $this->mailer->send($email);

    }
}