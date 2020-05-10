<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
 */
class Group
{
    const statusinfo = [
        'Public'=>0,
        'Privé' =>1,
        'invisible' => 2
    ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="createGroup")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=AffGroupe::class, mappedBy="groupe", cascade={"persist"})
     */
    private $affGroupes;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="groupe",cascade={"persist"})
     */
    private $posts;

    public function __construct()
    {
        $this->affGroupes = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|AffGroupe[]
     */
    public function getAffGroupes(): Collection
    {
        return $this->affGroupes;
    }

    public function addAffGroupe(AffGroupe $affGroupe): self
    {
        if (!$this->affGroupes->contains($affGroupe)) {
            $this->affGroupes[] = $affGroupe;
            $affGroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeAffGroupe(AffGroupe $affGroupe): self
    {
        if ($this->affGroupes->contains($affGroupe)) {
            $this->affGroupes->removeElement($affGroupe);
            // set the owning side to null (unless already changed)
            if ($affGroupe->getGroupe() === $this) {
                $affGroupe->setGroupe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setGroupe($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getGroupe() === $this) {
                $post->setGroupe(null);
            }
        }

        return $this;
    }
}
