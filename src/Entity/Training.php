<?php

namespace App\Entity;

use App\Repository\TrainingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TrainingRepository::class)
 */
class Training
{
    /**
     * @var UuidInterface
     * @Groups({"post_training","get_training"})
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @Groups({"post_training","get_training"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @Groups({"post_training", "get_training"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @Groups({"post_training","get_training"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @Groups("post_training")
     * @ORM\OneToMany(targetEntity=GpsLog::class, mappedBy="training")
     */
    private $log;

    /**
     * @ORM\OneToOne(targetEntity=Statistic::class, cascade={"persist", "remove"})
     */
    private $statistic;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="training")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=TrainingType::class)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="training")
     */
    private $images;

    public function __construct()
    {
        $this->log = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|GpsLog[]
     */
    public function getLog(): Collection
    {
        return $this->log;
    }

    public function addLog(GpsLog $log): self
    {
        if (!$this->log->contains($log)) {
            $this->log[] = $log;
            $log->setTraining($this);
        }

        return $this;
    }

    public function removeLog(GpsLog $log): self
    {
        if ($this->log->contains($log)) {
            $this->log->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getTraining() === $this) {
                $log->setTraining(null);
            }
        }

        return $this;
    }

    public function getStatistic(): ?statistic
    {
        return $this->statistic;
    }

    public function setStatistic(?statistic $statistic): self
    {
        $this->statistic = $statistic;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?TrainingType
    {
        return $this->type;
    }

    public function setType(?TrainingType $type): self
    {
        $this->type = $type;

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
            $image->setTraining($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getTraining() === $this) {
                $image->setTraining(null);
            }
        }

        return $this;
    }

}
