<?php

namespace App\Entity;

use App\Repository\StatisticRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
/**
 * @ORM\Entity(repositoryClass=StatisticRepository::class)
 */
class Statistic
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $kcal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxWidth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $minWidth;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $maxSpeed;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $minSpeed;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfBreaks;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getKcal(): ?int
    {
        return $this->kcal;
    }

    public function setKcal(?int $kcal): self
    {
        $this->kcal = $kcal;

        return $this;
    }

    public function getMaxWidth(): ?int
    {
        return $this->maxWidth;
    }

    public function setMaxWidth(?int $maxWidth): self
    {
        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function getMinWidth(): ?int
    {
        return $this->minWidth;
    }

    public function setMinWidth(?int $minWidth): self
    {
        $this->minWidth = $minWidth;

        return $this;
    }

    public function getMaxSpeed(): ?float
    {
        return $this->maxSpeed;
    }

    public function setMaxSpeed(?float $maxSpeed): self
    {
        $this->maxSpeed = $maxSpeed;

        return $this;
    }

    public function getMinSpeed(): ?float
    {
        return $this->minSpeed;
    }

    public function setMinSpeed(?float $minSpeed): self
    {
        $this->minSpeed = $minSpeed;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(?\DateTimeInterface $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getNumberOfBreaks(): ?int
    {
        return $this->numberOfBreaks;
    }

    public function setNumberOfBreaks(?int $numberOfBreaks): self
    {
        $this->numberOfBreaks = $numberOfBreaks;

        return $this;
    }
}
