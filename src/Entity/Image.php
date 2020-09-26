<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 */
class Image
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublic;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMain;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=GpsLog::class, inversedBy="images")
     */
    private $gpsLog;

    /**
     * @ORM\ManyToOne(targetEntity=Training::class, inversedBy="images")
     */
    private $training;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $oryginalFileName;

    /**
     * @return UuidInterface
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getIsMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGpsLog(): ?GpsLog
    {
        return $this->gpsLog;
    }

    public function setGpsLog(?GpsLog $gpsLog): self
    {
        $this->gpsLog = $gpsLog;

        return $this;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): self
    {
        $this->training = $training;

        return $this;
    }

    /**
     * @param UuidInterface $id
     */
    public function setId(): void
    {
        $id=$this->getId();
        if(!isset($id)) {
            $this->id = Uuid::uuid1();
        }
        $this->id = $this->getId();
    }

    public function getOryginalFileName(): ?string
    {
        return $this->oryginalFileName;
    }

    public function setOryginalFileName(string $oryginalFileName): self
    {
        $this->oryginalFileName = $oryginalFileName;

        return $this;
    }

    public function getCreatedAt(){
        return $this->getId()->getDateTime();
    }
}
