<?php

namespace App\Entity;

use App\Repository\GeneratedUrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass=GeneratedUrlRepository::class)
 */
class GeneratedUrl
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
    private $hash;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiredAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $entry=0;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="generatedUrls")
     */
    private $user;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash($lengthX2): self
    {
        $this->hash = bin2hex(random_bytes($lengthX2));
        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt($hour): self
    {
        $this->expiredAt = new \DateTime('now + '.$hour.' hour');

        return $this;
    }

    public function getEntry(): ?int
    {
        return $this->entry;
    }

    public function incrementEntry(): self
    {
        $this->entry =   $this->entry++;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isExpired(){

        if($this->expiredAt->getTimestamp() - time() > 0){
            return true;
        }

        return false;

    }
}
