<?php

namespace App\Entity;

use App\Repository\StatisticRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups("get_statistic")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("get_statistic")
     */
    private $kcal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("get_statistic")
     */
    private $maxHeight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("get_statistic")
     */
    private $minHeight;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("get_statistic")
     */
    private $maxSpeed;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("get_statistic")
     */
    private $minSpeed;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("get_statistic")
     */
    private $avgSpeed;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups("get_statistic")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("get_statistic")
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

    public function getNumberOfBreaks(): ?int
    {
        return $this->numberOfBreaks;
    }

    public function setNumberOfBreaks(?int $numberOfBreaks): self
    {
        $this->numberOfBreaks = $numberOfBreaks;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAvgSpeed()
    {
        return $this->avgSpeed;
    }

    /**
     * @param mixed $avgSpeed
     */
    public function setAvgSpeed($avgSpeed): void
    {
        $this->avgSpeed = $avgSpeed;
    }

    /**
     * @return mixed
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * @param mixed $maxHeight
     */
    public function setMaxHeight($maxHeight): void
    {
        $this->maxHeight = $maxHeight;
    }

    /**
     * @return mixed
     */
    public function getMinHeight()
    {
        return $this->minHeight;
    }

    /**
     * @param mixed $minHeight
     */
    public function setMinHeight($minHeight): void
    {
        $this->minHeight = $minHeight;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }
}
