<?php declare(strict_types=1);

namespace Pehapkari\Mailer;

use Pehapkari\Registration\Entity\TrainingRegistration;

final class Mailer
{
    public function sendRegistrationEmail(TrainingRegistration $trainingRegistration): void
    {
        dump($trainingRegistration);
        die;
    }
}
