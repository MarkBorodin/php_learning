<?php

namespace App\Controller;

use App\Message\MyEmailMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/email")
     */
    public function sendEmail(MessageBusInterface $bus)
    {
        $emailTo = 'rens2588@gmail.com';

        $this->bus->dispatch(new MyEmailMessage($emailTo));

        return $this->redirectToRoute('home');

    }
}