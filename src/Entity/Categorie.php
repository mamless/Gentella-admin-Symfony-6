<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 *
 * @UniqueEntity(fields={"libelle"})
 */
class Categorie
{
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private ?string $libelle = null;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="categories")
     */
    private ?Categorie $CategorieParente = null;

    /**
     * @ORM\OneToMany(targetEntity=Categorie::class, mappedBy="CategorieParente")
     */
    private PersistentCollection|array $categories;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $valid = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $deleted = null;

    /**
     * @ORM\ManyToMany(targetEntity=BlogPost::class, mappedBy="categories")
     */
    private PersistentCollection|array $blogPosts;

    public function __construct()
    {
        $this->categories = new PersistentCollection();
        $this->blogPosts = new PersistentCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getCategorieParente(): ?self
    {
        return $this->CategorieParente;
    }

    public function setCategorieParente(?self $CategorieParente): self
    {
        $this->CategorieParente = $CategorieParente;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setCategorieParente($this);
        }

        return $this;
    }

    public function removeCategory(self $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getCategorieParente() === $this) {
                $category->setCategorieParente(null);
            }
        }

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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->libelle;
    }

    /**
     * @return Collection|BlogPost[]
     */
    public function getBlogPosts(): Collection
    {
        return $this->blogPosts;
    }

    public function addBlogPost(BlogPost $blogPost): self
    {
        if (!$this->blogPosts->contains($blogPost)) {
            $this->blogPosts[] = $blogPost;
            $blogPost->addCategory($this);
        }

        return $this;
    }

    public function removeBlogPost(BlogPost $blogPost): self
    {
        if ($this->blogPosts->contains($blogPost)) {
            $this->blogPosts->removeElement($blogPost);
            $blogPost->removeCategory($this);
        }

        return $this;
    }
}
