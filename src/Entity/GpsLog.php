<?php

namespace App\Entity;

use App\Repository\GpsLogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GpsLogRepository::class)
 */
class GpsLog
{
    /**
     * @var UuidInterface
     *
     * @Groups({"post_gps_log", "get_gps_log"})
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;
    /**
     * @ORM\Column(type="float")
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="float")
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $speed;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $height;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $isStop;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"post_gps_log", "get_gps_log"})
     */
    private $isPaused;

    /**
     * @ORM\ManyToOne(targetEntity=Training::class, inversedBy="log")
     * @ORM\JoinColumn(nullable=false)
     */
    private $training;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="gpsLog")
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(?float $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsStop(): ?bool
    {
        return $this->isStop;
    }

    public function setIsStop(bool $isStop): self
    {
        $this->isStop = $isStop;

        return $this;
    }

    public function getIsPaused(): ?bool
    {
        return $this->isPaused;
    }

    public function setIsPaused(bool $isPaused): self
    {
        $this->isPaused = $isPaused;

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
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setGpsLog($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getGpsLog() === $this) {
                $image->setGpsLog(null);
            }
        }

        return $this;
    }
}
