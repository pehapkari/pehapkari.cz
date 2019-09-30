<?php declare(strict_types=1);

namespace Pehapkari\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

final class AdminMailerController extends AbstractController
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route(path="/admin/send-mail/", name="send_mail")
     * @see https://symfony.com/doc/current/mailer.html
     */
    public function sendMail(): void
    {
        $email = (new Email())
            ->from('tomas@pehapkari.cz')
            ->to('tomas.vot@gmail.com')
            ->replyTo('tomas@pehapkari.cz')
            ->subject('Díky za registraci!')
            ->text('Ahoj X, čekáme na Y, dáme vědět až. Pěkný den :)')
            ->html('<strong>testing');

        $this->mailer->send($email);

        echo 'it works! check the mailbox!';
        die;
    }
}
