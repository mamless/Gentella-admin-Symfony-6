<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use App\Traits\StateEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{

    use TimestampableEntity;

    use StateEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToMany(targetEntity: Role::class)]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'profile', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function addRoles(array $roles): self
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }


    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setProfile($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfile() === $this) {
                $user->setProfile(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNom();
    }


    public function oldify(): void
    {
        $this->nom .= '-old-'.$this->id;
    }


}
