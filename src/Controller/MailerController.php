<?php

namespace App\Controller;

use App\Message\MyEmailMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Dotenv\Dotenv;


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
    public function sendEmail(MessageBusInterface $bus): RedirectResponse
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');
        $emailTo = $_ENV['ADMIN_EMAIL'];

        $this->bus->dispatch(new MyEmailMessage($emailTo));

        return $this->redirectToRoute('home');
    }
}