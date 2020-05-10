<?php

namespace App\Entity;

use App\Repository\AffGroupeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AffGroupeRepository::class)
 */
class AffGroupe
{
    const lvlaff = [
        'attente'=>0,
        'membre'=>1,
        'moderateur'=>2,
    ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="affGroupes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="affGroupes", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupe(): ?Group
    {
        return $this->groupe;
    }

    public function setGroupe(?Group $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
