<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api\Factory;

use Defr\Ares;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Geo\FullAddressResolver;

final class SubjectDataFactory
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

    /**
     * @return mixed[]
     */
    public function createFromTrainingRegistration(TrainingRegistration $trainingRegistration): array
    {
        $data = [
            'name' => $this->createName($trainingRegistration),
        ];

        if ($trainingRegistration->getEmail() !== null) {
            $data['email'] = $trainingRegistration->getEmail();
        }

        if ($trainingRegistration->getPhone() !== null) {
            $data['phone'] = $trainingRegistration->getPhone();
        }

        if (is_numeric($trainingRegistration->getIco())) { // probably ICO
            $data['registration_no'] = $trainingRegistration->getIco();

            $aresRecord = $this->ares->findByIdentificationNumber($trainingRegistration->getIco());

            $data['street'] = $aresRecord->getStreetWithNumbers();
            $data['city'] = $aresRecord->getTown();
            $data['zip'] = $aresRecord->getZip();

            if ($aresRecord->getTaxId()) {
                $data['vat_no'] = $aresRecord->getTaxId();
            }
        } elseif ($trainingRegistration->getIco()) { // probably address
            $address = $trainingRegistration->getIco();

            $resolvedAddress = $this->fullAddressResolver->resolve($address);
            $data['street'] = $resolvedAddress['road'] . ' ' . $resolvedAddress['house_number'];
            $data['city'] = $resolvedAddress['city'] ?? $resolvedAddress['town'];
            $data['zip'] = $resolvedAddress['postcode'];
        }

        return $data;
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
}
