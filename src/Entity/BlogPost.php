<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[UniqueEntity(fields: ['titre'])]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $content = null;


    #[Gedmo\Slug(fields: ["titre"])]
    #[ORM\Column(unique: true)]
    private ?string $slug = null;

    #[ORM\Column(unique: true)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?string $blogImage = null;

    #[ORM\Column( nullable: true)]
    private ?DateTime $plubishedAt = null;

    #[ORM\Column]
    private ?bool $deleted = null;

    #[ORM\Column]
    private ?bool $valid = null;

    #[Gedmo\Timestampable(on:'create')]
    #[ORM\Column]
    private ?DateTime $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'blogPostsCreated')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;

    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'blogPosts')]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'blogPost', targetEntity: Historique::class)]
    private Collection $historiques;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->historiques = new ArrayCollection();
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

    public function getPlubishedAt(): ?DateTimeInterface
    {
        return $this->plubishedAt;
    }

    public function setPlubishedAt(?DateTimeInterface $plubishedAt): self
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

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
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
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection $categories
     */
    public function setCategories(Collection $categories): void
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
        return $this->getValid() && $this->plubishedAt < new DateTime() && $this->plubishedAt != null;
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

    public function oldify(): void
    {
        $this->titre .= '-old-'.$this->id;
    }
}
