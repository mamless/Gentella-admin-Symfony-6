<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 *
 * @UniqueEntity(fields={"titre"})
 */
class BlogPost
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
     * @ORM\Column(type="text")
     */
    private ?string $content = null;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     *
     * @Gedmo\Slug(fields={"titre"})
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     */
    private ?string $titre = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $blogImage = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $plubishedAt = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $deleted = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $valid = null;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPosts")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $author = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blogPostsCreated")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $creator = null;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="blogPosts")
     */
    private PersistentCollection|array $categories;

    /**
     * @ORM\OneToMany(targetEntity=Historique::class, mappedBy="blogPost")
     */
    private PersistentCollection|array $historiques;

    public function __construct()
    {
        $this->categories = new PersistentCollection();
        $this->historiques = new PersistentCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getBlogImage(): ?string
    {
        return $this->blogImage;
    }

    public function setBlogImage(string $blogImage): self
    {
        $this->blogImage = $blogImage;

        return $this;
    }

    public function getPlubishedAt(): ?\DateTimeInterface
    {
        return $this->plubishedAt;
    }

    public function setPlubishedAt(?\DateTimeInterface $plubishedAt): self
    {
        $this->plubishedAt = $plubishedAt;

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

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Categorie[] $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->getValid() && $this->plubishedAt < new \DateTime() && $this->plubishedAt != null;
    }

    /**
     * @return Collection|Historique[]
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): self
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques[] = $historique;
            $historique->setBlogPost($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getBlogPost() === $this) {
                $historique->setBlogPost(null);
            }
        }

        return $this;
    }

    public function oldify()
    {
        $this->titre .= '-old-'.$this->id;
    }
}
