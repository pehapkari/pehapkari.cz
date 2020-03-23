<?php

declare(strict_types=1);

namespace Pehapkari\Validation;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

final class EmailValidation
{
    private EmailValidator $emailValidator;

    private MultipleValidationWithAnd $multipleValidationWithAnd;

    public function __construct(EmailValidator $emailValidator)
    {
        $this->emailValidator = $emailValidator;
        $this->multipleValidationWithAnd = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation(),
        ]);
    }

    public function isEmailValid(string $email): bool
    {
        return $this->emailValidator->isValid($email, $this->multipleValidationWithAnd);
    }
}
