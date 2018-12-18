<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class AreaPrice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $cityCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $region;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $regionCode;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $flatPrice;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $housePrice;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $landPrice;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCityCode(): int
    {
        return $this->cityCode;
    }

    public function setCityCode(int $cityCode): void
    {
        $this->cityCode = $cityCode;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getRegionCode(): int
    {
        return $this->regionCode;
    }

    public function setRegionCode(int $regionCode): void
    {
        $this->regionCode = $regionCode;
    }

    public function getFlatPrice(): float
    {
        return $this->flatPrice;
    }

    public function setFlatPrice(float $flatPrice): void
    {
        $this->flatPrice = $flatPrice;
    }

    public function getHousePrice(): float
    {
        return $this->housePrice;
    }

    public function setHousePrice(float $housePrice): void
    {
        $this->housePrice = $housePrice;
    }

    public function getLandPrice(): float
    {
        return $this->landPrice;
    }

    public function setLandPrice(float $landPrice): void
    {
        $this->landPrice = $landPrice;
    }
}
