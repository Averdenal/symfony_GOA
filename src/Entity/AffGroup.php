<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AffGroupRepository")
 */
class AffGroup
{
    const role = [
        0=>'Membres',
        1=>'Modérateur',
        2=>'Super-Modérateur',
        3=>'Administrateur'
    ];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $role;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="affGroups", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Group", inversedBy="affGroupUser", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $groups;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGroups(): ?Group
    {
        return $this->groups;
    }

    public function setGroups(Group $goups): self
    {
        $this->goups = $groups;

        return $this;
    }
}
