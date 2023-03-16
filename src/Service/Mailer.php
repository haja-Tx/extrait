<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer as BaseMailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\MailerInterface;

class Mailer{

    private Environment $twig;
    private MailerInterface $mailer;
    private string $appMail;
    private string $appName;
    private string $appDsn;

    public function __construct(
        Environment $twig,
        string $appDsn,
        string $appName,
        string $appMail
    ) {

        $this->twig = $twig;
        $this->appDsn = $appDsn;
        $this->appMail = $appMail;
        $this->appName = $appName;
        
        $transport = Transport::fromDsn($appDsn);
        $this->mailer = new BaseMailer($transport);

    }

    public function createEmail(string $template, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from($this->appMail)
            ->html($html)
            ->text($text);
    }


    public function send(Email $email){

        $this->mailer->send($email);

    }


}