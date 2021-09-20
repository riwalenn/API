<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Categories
 *
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 */
#[
    ApiResource(
        collectionOperations: [
            'get' => ['normalization_context' => ['groups' => 'read']],
            'post',
        ],
        itemOperations: [
            'get' => ['normalization_context' => ['groups' => 'read']],
            'post',
            'put',
            'delete',
        ],
        denormalizationContext: ['groups' => ['write'], 'enable_max_depth' => true,],
        normalizationContext: ['groups' => ['read'], 'enable_max_depth' => true,],
    )]
class Categories
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
    #[
        Groups(['read', 'write']),
        Assert\NotBlank(message: "Vous devez saisir une catégorie", groups: ['write']),
        Assert\Length(min: 3, max: 255, minMessage: "Votre catégorie doit faire au minimum 10 caractères.", maxMessage: "Votre catégorie ne peux excéder 255 caractères.", groups: ['write'])
    ]
    private $value;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['write']),
        Assert\NotBlank(message: "Vous devez saisir une css", groups: ['write']),
        Assert\Length(min: 3, max: 255, minMessage: "Votre css doit faire au minimum 10 caractères.", maxMessage: "Votre css ne peux excéder 255 caractères.", groups: ['write'])
    ]
    private $css;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['write']),
        Assert\NotBlank(message: "Vous devez saisir une couleur", groups: ['write']),
        Assert\Length(min: 3, max: 255, minMessage: "Votre couleur doit faire au minimum 10 caractères.", maxMessage: "Votre couleur ne peux excéder 255 caractères.", groups: ['write'])
    ]
    private $color;

    /**
     * @ORM\OneToMany(targetEntity=Posts::class, mappedBy="category", orphanRemoval=true)
     */
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function setCss(string $css): self
    {
        $this->css = $css;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

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
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }
}
