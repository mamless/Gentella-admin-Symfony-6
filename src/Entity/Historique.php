<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne( inversedBy: 'historiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column( length: 255)]
    private ?string $action = null;

    #[Gedmo\Timestampable(on:'create')]
    #[ORM\Column]
    private ?DateTime $actionDate = null;

    #[ORM\ManyToOne( inversedBy: 'historiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BlogPost $blogPost = null;

    #[ORM\OneToOne(inversedBy: 'historique', targetEntity: OldPost::class, cascade: ['persist', 'remove'])]
    private ?OldPost $oldPost = null;

    public function getId(): int
    {
        return $this->id;
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

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getActionDate(): ?DateTimeInterface
    {
        return $this->actionDate;
    }

    public function setActionDate(DateTimeInterface $actionDate): self
    {
        $this->actionDate = $actionDate;

        return $this;
    }

    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    public function setBlogPost(?BlogPost $blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function getOldPost(): ?OldPost
    {
        return $this->oldPost;
    }

    public function setOldPost(?OldPost $oldPost): self
    {
        $this->oldPost = $oldPost;

        return $this;
    }
}
