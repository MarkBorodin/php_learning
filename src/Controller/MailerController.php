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
        // get .env vars
        $dotenv = new Dotenv();
        // load from .env file (need path to .env)
        $dotenv->load(__DIR__ . '/../../.env');
        // get some var
        $emailTo = $_ENV['ADMIN_EMAIL'];
        // async email sending
        $this->bus->dispatch(new MyEmailMessage($emailTo));

        return $this->redirectToRoute('home');
    }
}