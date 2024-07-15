<?php

namespace App\Entity;

use App\Repository\AppContactRepository;
use App\Traits\StateEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AppContactRepository::class)]
class AppContact
{
    use StateEntity;
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('mail')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('mail')]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    #[Groups('mail')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups('mail')]
    private ?string $object = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups('mail')]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    #[Groups('mail')]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups('mail')]
    private ?bool $isRead = null;

    #[ORM\Column]
    #[Groups('mail')]
    private ?bool $deleted = null;

    #[ORM\Column]
    #[Groups('mail')]
    private ?\DateTimeImmutable $sendedAt = null;

    #[ORM\Column]
    #[Groups('mail')]
    private ?bool $isAnswered = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
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

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getSendedAt(): ?\DateTimeImmutable
    {
        return $this->sendedAt;
    }

    public function setSendedAt(\DateTimeImmutable $sendedAt): self
    {
        $this->sendedAt = $sendedAt;

        return $this;
    }

    public function isIsAnswered(): ?bool
    {
        return $this->isAnswered;
    }

    public function setIsAnswered(bool $isAnswered): self
    {
        $this->isAnswered = $isAnswered;

        return $this;
    }
}
