<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Posts
 *
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 */
class Posts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $kicker;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modified_at;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="smallint")
     */
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=FavoritesPosts::class, mappedBy="Post", orphanRemoval=true)
     */
    private $favoritesPosts;

    public function __construct()
    {
        $this->favoritesPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getKicker(): ?string
    {
        return $this->kicker;
    }

    public function setKicker(?string $kicker): self
    {
        $this->kicker = $kicker;

        return $this;
    }

    public function getAuthor(): ?Users
    {
        return $this->author;
    }

    public function setAuthor(?Users $author): self
    {
        $this->author = $author;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modified_at;
    }

    public function setModifiedAt(\DateTimeInterface $modified_at): self
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection|FavoritesPosts[]
     */
    public function getFavoritesPosts(): Collection
    {
        return $this->favoritesPosts;
    }

    public function addFavoritesPost(FavoritesPosts $favoritesPost): self
    {
        if (!$this->favoritesPosts->contains($favoritesPost)) {
            $this->favoritesPosts[] = $favoritesPost;
            $favoritesPost->setPost($this);
        }

        return $this;
    }

    public function removeFavoritesPost(FavoritesPosts $favoritesPost): self
    {
        if ($this->favoritesPosts->removeElement($favoritesPost)) {
            // set the owning side to null (unless already changed)
            if ($favoritesPost->getPost() === $this) {
                $favoritesPost->setPost(null);
            }
        }

        return $this;
    }
}
