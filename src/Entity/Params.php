<?php

namespace App\Entity;

use App\Repository\ParamsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParamsRepository::class)]
class Params
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $useTerms = null;

    #[ORM\Column]
    private ?int $realId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUseTerms(): ?string
    {
        return $this->useTerms;
    }

    public function setUseTerms(?string $useTerms): self
    {
        $this->useTerms = $useTerms;

        return $this;
    }

    public function getRealId(): ?int
    {
        return $this->realId;
    }

    public function setRealId(int $realId): self
    {
        $this->realId = $realId;

        return $this;
    }

    public function init(): void
    {
        $this->setUseTerms("vide");
    }
}
