<?php

namespace App\Entity;

use App\Repository\AppFAQRepository;
use App\Traits\StateEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppFAQRepository::class)]
class AppFAQ
{

    use StateEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $question = null;

    #[ORM\Column(type: 'text')]
    private ?string $answer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }
}
