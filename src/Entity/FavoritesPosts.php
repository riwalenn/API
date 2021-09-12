<?php

namespace App\Entity;

use App\Repository\FavoritesPostsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FavoritesPostsRepository::class)
 */
class FavoritesPosts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="favoritesPosts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="favoritesPosts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Post;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->User;
    }

    public function setUser(?Users $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getPost(): ?Posts
    {
        return $this->Post;
    }

    public function setPost(?Posts $Post): self
    {
        $this->Post = $Post;

        return $this;
    }
}
