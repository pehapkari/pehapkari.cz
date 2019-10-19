<?php

declare(strict_types=1);

namespace Pehapkari\Mailer;

use Pehapkari\Training\Entity\TrainingFeedback;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * @see https://symfony.com/doc/current/mailer.html
 */
final class PehapkariMailer
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
     * @param TrainingFeedback[] $feedbacks
     */
    public function sendProvisionAndFeedbacksToTrainer(
        float $trainerProvision,
        array $feedbacks,
        string $trainerEmail
    ): void {
        $email = $this->createEmail();
        $email->to($trainerEmail);
        $email->subject('Pošli nám fakturu za školení!');
        $email->htmlTemplate('email/provision_and_trainer.twig');
        $email->context([
            'trainer_provision' => $trainerProvision,
            'feedbacks' => $feedbacks,
        ]);

        $this->mailer->send($email);
    }

    /**
     * @see https://symfony.com/doc/current/mailer.html#html-content
     */
    private function createEmail(): TemplatedEmail
    {
        $email = new TemplatedEmail();
        $email->from(new Address('tomas@pehapkari.cz', 'Tomáš Votruba'));

        // for testing
        $email->addBcc(new Address('tomas.vot@gmail.com'));

        return $email;
    }
}
