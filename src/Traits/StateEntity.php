<?php

namespace App\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait StateEntity
{
    #[ORM\Column]
    #[Groups('minimum')]
    private ?bool $deleted = null;

    #[ORM\Column]
    #[Groups('minimum')]
    private ?bool $valid = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $modfiedBy;


    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }


    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getModfiedBy(): ?User
    {
        return $this->modfiedBy;
    }

    public function setModfiedBy(?User $modfiedBy): self
    {
        $this->modfiedBy = $modfiedBy;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
    public function isDeleted(): bool
    {
        return $this->getDeleted();
    }

}