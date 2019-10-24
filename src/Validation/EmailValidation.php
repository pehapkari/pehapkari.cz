<?php

declare(strict_types=1);

namespace Pehapkari\Validation;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;

final class EmailValidation
{
    /**
     * @var EmailValidator
     */
    private $emailValidator;

    /**
     * @var MultipleValidationWithAnd
     */
    private $multipleValidationWithAnd;

    public function __construct(EmailValidator $emailValidator)
    {
        $this->emailValidator = $emailValidator;
        $this->multipleValidationWithAnd = new MultipleValidationWithAnd([new RFCValidation(), new DNSCheckValidation()]);
    }

    public function validateEmail(string $email): bool
    {
        return $this->emailValidator->isValid($email, $this->multipleValidationWithAnd);
    }
}
