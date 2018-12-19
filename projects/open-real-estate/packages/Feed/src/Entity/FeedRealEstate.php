<?php declare(strict_types=1);

namespace OpenRealEstate\Feed\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity
 */
class FeedRealEstate
{
    use Timestampable;
    use Blameable;

    // extra dům

    /**
     * @ORM\Column(type="string", length=255, name="poloha_domu")
     * @var string
     */
    private $houseLocation;

    // extra pozemek

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="cena_za_m²")
     * @var string
     */
    private $pricePerSquareMeter;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="energeticka_narocnost_budovy")
     * @var string
     */
    private $electricityRequirements;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="bezbarierovy")
     * @var string
     */
    private $barrierAccess;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="komunikace")
     * @var string
     */
    private $communication;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="telekomunikace")
     * @var string
     */
    private $telecommunication;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="odpad")
     * @var string
     */
    private $waste;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="garaz")
     * @var string
     */
    private $garage;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="parkovani")
     * @var string
     */
    private $parking;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="plocha_zahrady")
     * @var string
     */
    private $gardenArea;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="plocha_pozemku")
     * @var string
     */
    private $flatArea;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="plocha_zastavena")
     * @var string
     */
    private $houseArea;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="typ_domu")
     * @var string
     */
    private $houseType;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="vybaveni")
     * @var string
     */
    private $contents;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true, name="popis")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="plyn")
     * @var string
     */
    private $gas;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="topeni")
     * @var string
     */
    private $heating;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="voda")
     * @var string
     */
    private $water;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="sklep")
     * @var string
     */
    private $celar;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="lodzie")
     * @var string
     */
    private $lodzie;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="plocha_podlahova")
     * @var string
     */
    private $floorArea;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="uzitna_plocha")
     * @var string
     */
    private $usedArea;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="podlazi")
     * @var string
     */
    private $level;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="vlastnictvi")
     * @var string
     */
    private $ownership;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="stav_objektu")
     * @var string
     */
    private $objectState;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="stavba")
     * @var string
     */
    private $building;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="celkova_cena")
     * @var string
     */
    private $price;

    /**
     * @ORM\Column(type="string", nullable=true, length=255, name="elektrina")
     * @var string
     */
    private $electricity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(string $building): void
    {
        $this->building = $building;
    }

    public function getObjectState(): ?string
    {
        return $this->objectState;
    }

    public function setObjectState(string $objectState): void
    {
        $this->objectState = $objectState;
    }

    public function getOwnership(): ?string
    {
        return $this->ownership;
    }

    public function setOwnership(string $ownership): void
    {
        $this->ownership = $ownership;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getUsedArea(): ?string
    {
        return $this->usedArea;
    }

    public function setUsedArea(string $usedArea): void
    {
        $this->usedArea = $usedArea;
    }

    public function getFloorArea(): ?string
    {
        return $this->floorArea;
    }

    public function setFloorArea(string $floorArea): void
    {
        $this->floorArea = $floorArea;
    }

    public function getLodzie(): ?string
    {
        return $this->lodzie;
    }

    public function setLodzie(string $lodzie): void
    {
        $this->lodzie = $lodzie;
    }

    public function getCelar(): ?string
    {
        return $this->celar;
    }

    public function setCelar(string $celar): void
    {
        $this->celar = $celar;
    }

    public function getWater(): ?string
    {
        return $this->water;
    }

    public function setWater(string $water): void
    {
        $this->water = $water;
    }

    public function getHeating(): ?string
    {
        return $this->heating;
    }

    public function setHeating(string $heating): void
    {
        $this->heating = $heating;
    }

    public function getGas(): ?string
    {
        return $this->gas;
    }

    public function setGas(string $gas): void
    {
        $this->gas = $gas;
    }

    public function getElectricity(): ?string
    {
        return $this->electricity;
    }

    public function setElectricity(string $electricity): void
    {
        $this->electricity = $electricity;
    }

    public function getElectricityRequirements(): ?string
    {
        return $this->electricityRequirements;
    }

    public function setElectricityRequirements(string $electricityRequirements): void
    {
        $this->electricityRequirements = $electricityRequirements;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setContents(string $contents): void
    {
        $this->contents = $contents;
    }

    public function getHouseLocation(): ?string
    {
        return $this->houseLocation;
    }

    public function setHouseLocation(string $houseLocation): void
    {
        $this->houseLocation = $houseLocation;
    }

    public function getHouseType(): ?string
    {
        return $this->houseType;
    }

    public function setHouseType(string $houseType): void
    {
        $this->houseType = $houseType;
    }

    public function getHouseArea(): ?string
    {
        return $this->houseArea;
    }

    public function setHouseArea(string $houseArea): void
    {
        $this->houseArea = $houseArea;
    }

    public function getFlatArea(): ?string
    {
        return $this->flatArea;
    }

    public function setFlatArea(string $flatArea): void
    {
        $this->flatArea = $flatArea;
    }

    public function getGardenArea(): ?string
    {
        return $this->gardenArea;
    }

    public function setGardenArea(string $gardenArea): void
    {
        $this->gardenArea = $gardenArea;
    }

    public function getParking(): ?string
    {
        return $this->parking;
    }

    public function setParking(string $parking): void
    {
        $this->parking = $parking;
    }

    public function getGarage(): ?string
    {
        return $this->garage;
    }

    public function setGarage(string $garage): void
    {
        $this->garage = $garage;
    }

    public function getWaste(): ?string
    {
        return $this->waste;
    }

    public function setWaste(string $waste): void
    {
        $this->waste = $waste;
    }

    public function getTelecommunication(): ?string
    {
        return $this->telecommunication;
    }

    public function setTelecommunication(string $telecommunication): void
    {
        $this->telecommunication = $telecommunication;
    }

    public function getCommunication(): ?string
    {
        return $this->communication;
    }

    public function setCommunication(string $communication): void
    {
        $this->communication = $communication;
    }

    public function getBarrierAccess(): ?string
    {
        return $this->barrierAccess;
    }

    public function setBarrierAccess(string $barrierAccess): void
    {
        $this->barrierAccess = $barrierAccess;
    }

    public function getPricePerSquareMeter(): ?string
    {
        return $this->pricePerSquareMeter;
    }

    public function setPricePerSquareMeter(string $pricePerSquareMeter): void
    {
        $this->pricePerSquareMeter = $pricePerSquareMeter;
    }
}
