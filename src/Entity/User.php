<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @var UuidInterface
     * @Groups("getForgotPasswordHash")
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $editedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToOne(targetEntity=Profile::class, cascade={"persist", "remove"},fetch="EAGER")
     */
    private $profile;

    /**
     * @ORM\OneToMany(targetEntity=Training::class, mappedBy="user", orphanRemoval=true)
     */
    private $training;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=GeneratedUrl::class, mappedBy="user")
     */
    private $generatedUrls;

    public function __construct()
    {
        $this->training = new ArrayCollection();
        $this->generatedUrls = new ArrayCollection();
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEditedAt(): ?\DateTimeInterface
    {
        return $this->editedAt;
    }

    public function setEditedAt(?\DateTimeInterface $editedAt): self
    {
        $this->editedAt = $editedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getProfile(): ?profile
    {
        return $this->profile;
    }

    public function setProfile(?profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection|training[]
     */
    public function getTraining(): Collection
    {
        return $this->training;
    }

    public function addTraining(training $training): self
    {
        if (!$this->training->contains($training)) {
            $this->training[] = $training;
            $training->setUser($this);
        }

        return $this;
    }

    public function removeTraining(training $training): self
    {
        if ($this->training->contains($training)) {
            $this->training->removeElement($training);
            // set the owning side to null (unless already changed)
            if ($training->getUser() === $this) {
                $training->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }


    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Collection|GeneratedUrl[]
     */
    public function getGeneratedUrls(): Collection
    {
        return $this->generatedUrls;
    }

    public function addGeneratedUrl(GeneratedUrl $generatedUrl): self
    {
        if (!$this->generatedUrls->contains($generatedUrl)) {
            $this->generatedUrls[] = $generatedUrl;
            $generatedUrl->setUser($this);
        }

        return $this;
    }

    public function removeGeneratedUrl(GeneratedUrl $generatedUrl): self
    {
        if ($this->generatedUrls->contains($generatedUrl)) {
            $this->generatedUrls->removeElement($generatedUrl);
            // set the owning side to null (unless already changed)
            if ($generatedUrl->getUser() === $this) {
                $generatedUrl->setUser(null);
            }
        }

        return $this;
    }
}
