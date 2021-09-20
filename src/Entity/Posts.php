<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Posts
 *
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 */
#[
    ApiResource(
        collectionOperations: [
            'get' => ['normalization_context' => ['groups' => 'post:read']],
            'post',
        ],
        itemOperations: [
            'get' => ['normalization_context' => ['groups' => 'post:read']],
            'post',
            'put',
            'delete',
        ],
        denormalizationContext: ['groups' => ['post:write'], 'enable_max_depth' => true,],
        normalizationContext: ['groups' => ['post:read'], 'enable_max_depth' => true,],
        paginationItemsPerPage: 3,
    ),
ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])]
class Posts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[
        Groups(['post:read']),
    ]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['post:read', 'post:write']),
        Assert\NotBlank(message: "Vous devez saisir un titre", groups: ['post:write']),
        Assert\Length(min: 10, max: 255, minMessage: "Votre titre doit faire au minimum 10 caractères.", maxMessage: "Votre titre ne peux excéder 255 caractères.", groups: ['post:write'])
    ]
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[
        Groups(['post:read', 'post:write']),
        Assert\NotBlank(message: "Vous devez saisir un kicker", groups: ['post:write']),
        Assert\Length(min: 10, max: 255, minMessage: "Votre kicker doit faire au minimum 10 caractères.", maxMessage: "Votre kicker ne peux excéder 255 caractères.", groups: ['post:write'])
    ]
    private $kicker;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    #[
        Groups(['post:read', 'post:write']),
    ]
    private $author;

    /**
     * @ORM\Column(type="text")
     */
    #[
        Groups(['post:read', 'post:write']),
        Assert\NotBlank(message: "Vous devez saisir un contenu", groups: ['post:write']),
        Assert\Length(min: 10, minMessage: "Votre contenu doit faire au minimum 10 caractères.", groups: ['post:write'])
    ]
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    #[
        Groups(['post:read', 'post:write']),
    ]
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    #[
        Groups(['post:read', 'post:write']),
    ]
    private $modified_at;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    #[
        Groups(['post:read', 'post:write']),
        Assert\Valid(),
    ]
    private $category;

    /**
     * @ORM\Column(type="smallint")
     */
    #[
        Groups(['post:write']),
    ]
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=FavoritesPosts::class, mappedBy="Post", orphanRemoval=true)
     */
    private $favoritesPosts;

    public function __construct()
    {
        $this->favoritesPosts = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->modified_at = new \DateTime();
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
            // set the owning side to null (unless alpost:ready changed)
            if ($favoritesPost->getPost() === $this) {
                $favoritesPost->setPost(null);
            }
        }

        return $this;
    }
}
