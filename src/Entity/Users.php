<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;
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
            'get' => ['normalization_context' => ['groups' => 'read']],
            'post',
        ],
        itemOperations: [
            'get' => ['normalization_context' => ['groups' => 'read']],
            'put',
            'delete',
        ],
        denormalizationContext: ['groups' => ['write'], 'enable_max_depth' => true,],
        normalizationContext: ['groups' => ['read'], 'enable_max_depth' => true,],
    )]
class Users implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_AUTHOR = 'ROLE_AUTHOR';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read", "write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur")
     * @Groups({"read", "write"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(message="Vous devez saisir une adresse email.")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte.")
     * @Groups({"read", "write"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Vous devez saisir un mot de passe")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "write"})
     */
    private $modified_at;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read", "write"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"read", "write"})
     */
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
            // set the owning side to null (unless already changed)
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
            // set the owning side to null (unless already changed)
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
