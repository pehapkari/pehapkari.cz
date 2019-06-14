<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api\Factory;

use Defr\Ares;
use K0nias\FakturoidApi\Model\Subject\OptionalParameters;
use K0nias\FakturoidApi\Model\Subject\Subject;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Geo\FullAddressResolver;

final class SubjectApiObjectFactory
{
    /**
     * @var Ares
     */
    private $ares;

    /**
     * @var FullAddressResolver
     */
    private $fullAddressResolver;

    public function __construct(Ares $ares, FullAddressResolver $fullAddressResolver)
    {
        $this->ares = $ares;
        $this->fullAddressResolver = $fullAddressResolver;
    }

    public function createFromTrainingsRegistration(TrainingRegistration $trainingRegistration): Subject
    {
        $name = $this->createName($trainingRegistration);
        $subjectOptionalParameters = $this->createOptionalParameters($trainingRegistration);

        return new Subject($name, $subjectOptionalParameters);
    }

    private function createName(TrainingRegistration $trainingRegistration): string
    {
        if (is_numeric($trainingRegistration->getIco())) { // probably ICO
            $aresRecord = $this->ares->findByIdentificationNumber($trainingRegistration->getIco());

            // prefer company name in ARES
            return $aresRecord->getCompanyName();
        }

        return (string) $trainingRegistration->getName();
    }

    private function createOptionalParameters(TrainingRegistration $trainingRegistration): OptionalParameters
    {
        $subjectOptionalParameters = new OptionalParameters();

        if ($trainingRegistration->getEmail() !== null) {
            $subjectOptionalParameters->email($trainingRegistration->getEmail());
        }

        if ($trainingRegistration->getPhone() !== null) {
            $subjectOptionalParameters->phone($trainingRegistration->getPhone());
        }

        if ($trainingRegistration->getIco() === null) {
            return $subjectOptionalParameters;
        }

        if (is_numeric($trainingRegistration->getIco())) { // probably ICO
            $subjectOptionalParameters->registrationNumber($trainingRegistration->getIco());
            $aresRecord = $this->ares->findByIdentificationNumber($trainingRegistration->getIco());

            $subjectOptionalParameters->street($aresRecord->getStreetWithNumbers());
            $subjectOptionalParameters->city($aresRecord->getTown());
            $subjectOptionalParameters->zip($aresRecord->getZip());

            if ($aresRecord->getTaxId()) {
                $subjectOptionalParameters->vatNumber($aresRecord->getTaxId());
            }

            return $subjectOptionalParameters;
        }

        // probably address
        $address = $trainingRegistration->getIco();

        $resolvedAddress = $this->fullAddressResolver->resolve($address);
        $subjectOptionalParameters->street($resolvedAddress['road'] . ' ' . $resolvedAddress['house_number']);

        $subjectOptionalParameters->city($resolvedAddress['city'] ?? $resolvedAddress['town']);
        $subjectOptionalParameters->zip($resolvedAddress['postcode']);

        return $subjectOptionalParameters;
    }
}
