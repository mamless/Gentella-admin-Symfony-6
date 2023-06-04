<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'])]
#[UniqueEntity(fields: ['email'])]
class User implements UserInterface, EquatableInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Ne doit pas être vide')]
    private ?string $username;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Ne doit pas être vide')]
    private ?string $nomComplet= null;

    #[ORM\Column( length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Ne doit pas être vide')]
    #[Assert\Email(message: 'Email invalide')]
    private ?string $email= null;

    #[ORM\Column]
    private ?bool $valid = null;

    #[ORM\Column]
    private ?bool $deleted = null;

    #[ORM\Column( length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: BlogPost::class)]
    private Collection $blogPosts;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: BlogPost::class)]
    private Collection $blogPostsCreated;

    #[ORM\Column(type: 'boolean')]
    private ?bool $admin = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Historique::class)]
    private Collection $historiques;

    public function __construct()
    {
        $this->blogPosts = new ArrayCollection();
        $this->blogPostsCreated = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function setUsername($username): self
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
    public function getPassword(): ?string
    {
        return $this->password;
    }



    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet($nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

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

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }


    public function getAvatarUrl(): string
    {
        return "https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=".$this->username;
    }

    public function getColorCode(): string
    {
        $code = dechex(crc32($this->getUsername()));
        $code = substr($code, 0, 6);

        return '#'.$code;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        /*if (strlen($this->password)< 3){
            $context->buildViolation('Mot de passe trop court')
                ->atPath('justpassword')
                ->addViolation();
        }*/
    }

    /**
     * @return Collection
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->contains($blogPost)) {
            $this->blogPosts->removeElement($blogPost);
            // set the owning side to null (unless already changed)
            if ($blogPost->getAuthor() === $this) {
                $blogPost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getBlogPostsCreated(): Collection
    {
        return $this->blogPostsCreated;
    }

    public function addBlogPostsCreated(BlogPost $blogPostsCreated): self
    {
        if (!$this->blogPostsCreated->contains($blogPostsCreated)) {
            $this->blogPostsCreated[] = $blogPostsCreated;
            $blogPostsCreated->setCreator($this);
        }

        return $this;
    }

    public function removeBlogPostsCreated(BlogPost $blogPostsCreated): self
    {
        if ($this->blogPostsCreated->contains($blogPostsCreated)) {
            $this->blogPostsCreated->removeElement($blogPostsCreated);
            // set the owning side to null (unless already changed)
            if ($blogPostsCreated->getCreator() === $this) {
                $blogPostsCreated->setCreator(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return "$this->nomComplet ($this->id)";
    }

    public function isAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): self
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques[] = $historique;
            $historique->setUser($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getUser() === $this) {
                $historique->setUser(null);
            }
        }

        return $this;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if ($user instanceof User) {
            return $this->isValid() && !$this->isDeleted() && $this->getPassword() == $user->getPassword() && $this->getUsername() == $user->getUsername()
                && $this->getEmail() == $user->getEmail();
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getSalt(): ?string
    {
        //not used here
        return null;
    }
}
