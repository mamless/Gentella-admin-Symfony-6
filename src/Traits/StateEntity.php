<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait StateEntity
{
    #[ORM\Column]
    private ?bool $deleted = null;

    #[ORM\Column]
    private ?bool $valid = null;


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
}