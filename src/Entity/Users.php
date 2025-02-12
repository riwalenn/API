<?php

namespace App\Entity;

use App\Controller\UserStateController;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Users
 *
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @method string getUserIdentifier()
 */
#[
    ApiResource(
        collectionOperations: [
            'get' => ['normalization_context' => ['groups' => 'user:read']],
            'post' => ['validation_groups' => ['user:write']],
        ],
        itemOperations: [
            'get' => ['normalization_context' => ['groups' => 'user:read']],
            'put',
            'delete',
            'validation' => [
              'method' => 'POST',
              'path' => '/users/{id}/validation',
              'controller' => UserStateController::class,
                'openapi_context' => [
                    'summary' => 'Approve a users resource.',
                    'requestBody' => [
                        'content' => [
                            'application/json' => [
                                'schema' => []
                            ]
                        ]
                    ]
                ]
            ],
        ],
        denormalizationContext: ['groups' => ['user:write'], 'enable_max_depth' => true,],
        normalizationContext: ['groups' => ['user:read'], 'enable_max_depth' => true,],
    )]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_AUTHOR = 'ROLE_AUTHOR';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     */
    #[
        Groups(['user:read', 'user:write']),
        Assert\Length(min: 10, max: 40, minMessage: "Vous devez saisir au moins 10 caractères", maxMessage: "Vous ne pouvez saisir que 40 caractères au maximum.", groups: ['user:write']),
        Assert\NotBlank(message: "Vous devez saisir un nom d'utilisateur", groups: ['user:write'])
    ]
    private $username;

    /**
     * @ORM\Column(type="string", length=60)
     */
    #[
        Groups(['user:write']),
        Assert\NotBlank(message: "Vous devez saisir une adresse email !", groups: ['user:write']),
        Assert\Email(message: "Le format de l'adresse n'est pas correcte.", groups: ['user:write'])
    ]
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    #[
        Groups(['user:write']),
        Assert\NotBlank(message: "Vous devez saisir un mot de passe", groups: ['user:write']),
        Assert\Length(min: 12, max: 64, minMessage: "Vous devez saisir un mot de passe de minimum 12 caractères.", maxMessage: "Votre mot de passe ne peut dépasser 64 caractères.", groups: ['user:write'])
    ]
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    #[
        Groups(['user:write']),
    ]
    private $modified_at;

    /**
     * @ORM\Column(type="datetime")
     */
    #[
        Groups(['user:write']),
    ]
    private $created_at;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    #[
        Groups(['user:write']),
    ]
    private $state;

    /**
     * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="author", orphanRemoval=true, cascade={"persist"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=FavoritesPosts::class, mappedBy="User", orphanRemoval=true)
     */
    private $favoritesPosts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->favoritesPosts = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->modified_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getRoles(): ?array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Collection|Posts[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless aluser:ready changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

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
            $favoritesPost->setUser($this);
        }

        return $this;
    }

    public function removeFavoritesPost(FavoritesPosts $favoritesPost): self
    {
        if ($this->favoritesPosts->removeElement($favoritesPost)) {
            // set the owning side to null (unless aluser:ready changed)
            if ($favoritesPost->getUser() === $this) {
                $favoritesPost->setUser(null);
            }
        }

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        //eraseCredentials() method
    }

    public function __call($name, $arguments)
    {
        //Implement @method string getUserIdentifier()
    }
}
