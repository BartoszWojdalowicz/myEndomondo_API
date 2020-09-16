<?php

namespace App\Entity;

use App\Repository\TrainingTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=TrainingTypeRepository::class)
 */
class TrainingType
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
     * @ORM\Column(type="float", nullable=true)
     */
    private $womenMultipler;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $menMultipler;

    public function __construct()
    {
        $this->Training = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getWomenMultipler(): ?float
    {
        return $this->womenMultipler;
    }

    public function setWomenMultipler(?float $womenMultipler): self
    {
        $this->womenMultipler = $womenMultipler;

        return $this;
    }

    public function getMenMultipler(): ?float
    {
        return $this->menMultipler;
    }

    public function setMenMultipler(?float $menMultipler): self
    {
        $this->menMultipler = $menMultipler;

        return $this;
    }
}
