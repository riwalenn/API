<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FavoritesPostsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * FavoritesPosts
 *
 * @ORM\Entity(repositoryClass=FavoritesPostsRepository::class)
 */
#[
    ApiResource(
        collectionOperations: [
            'get' => ['normalization_context' => ['groups' => 'fav:read']],
            'post',
        ],
        itemOperations: [
            'get' => ['normalization_context' => ['groups' => 'fav:read']],
            'put',
            'delete',
        ],
        denormalizationContext: ['groups' => ['fav:write'], 'enable_max_depth' => true,],
        normalizationContext: ['groups' => ['fav:read'], 'enable_max_depth' => true,],
    )]
class FavoritesPosts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[
        Groups(['fav:read']),
    ]
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
