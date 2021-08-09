<?php

namespace App\Message;

class MyEmailMessage
{
    private string $emailTo;

    public function __construct(string $email_to)
    {
        $this->emailTo = $email_to;
    }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }
}
