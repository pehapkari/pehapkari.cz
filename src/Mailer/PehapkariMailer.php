<?php

declare(strict_types=1);

namespace Pehapkari\Mailer;

use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Training\Entity\TrainingFeedback;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * @see https://symfony.com/doc/current/mailer.html
 */
final class PehapkariMailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRegistrationConfirmation(TrainingRegistration $trainingRegistration): void
    {
        $training = $trainingRegistration->getTraining();

        $email = $this->createEmail();
        $email->to($trainingRegistration->getEmail());
        $email->subject(sprintf('Vítej na školení %s', $training->getName()));

        // set templates with variables
        $email->htmlTemplate('email/email_welcome_to_training.twig');
        $email->context([
            'training' => $trainingRegistration->getTraining(),
            'training_term' => $trainingRegistration->getTrainingTerm(),
        ]);

        $this->mailer->send($email);
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
        $email->htmlTemplate('email/email_provision_and_trainer.twig');
        $email->context([
            'trainer_provision' => $trainerProvision,
            'feedbacks' => $feedbacks,
        ]);

        $this->mailer->send($email);
    }

    public function sendFeedbackFormToRegistrations(TrainingTerm $trainingTerm): void
    {
        $email = $this->createEmail();

        foreach ($trainingTerm->getRegistrations() as $registration) {
            $email->addBcc($registration->getEmail());
        }

        $email->subject('Dej nám feedback za školení');
        $email->htmlTemplate('email/email_feedback.twig');
        $email->context([
            'training' => $trainingTerm->getTraining(),
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
