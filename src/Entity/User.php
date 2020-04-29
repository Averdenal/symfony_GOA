<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkFacebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkTwitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkInstagram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkYoutube;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkDiscord;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkTwitch;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="createdBy")
     */
    private $createGroup;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="createdBy")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="profile")
     */
    private $profilePost;

    public function __construct()
    {
        $this->createGroup = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->profilePost = new ArrayCollection();
    }


    /**
     * @return int|null
     * @see UserInterface
     */
    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return $this
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return $this
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     * @return $this
     */
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getLinkFacebook(): ?string
    {
        return $this->linkFacebook;
    }

    public function setLinkFacebook(?string $linkFacebook): self
    {
        $this->linkFacebook = $linkFacebook;

        return $this;
    }

    public function getLinkTwitter(): ?string
    {
        return $this->linkTwitter;
    }

    public function setLinkTwitter(?string $linkTwitter): self
    {
        $this->linkTwitter = $linkTwitter;

        return $this;
    }

    public function getLinkInstagram(): ?string
    {
        return $this->linkInstagram;
    }

    public function setLinkInstagram(?string $linkInstagram): self
    {
        $this->linkInstagram = $linkInstagram;

        return $this;
    }

    public function getLinkYoutube(): ?string
    {
        return $this->linkYoutube;
    }

    public function setLinkYoutube(?string $linkYoutube): self
    {
        $this->linkYoutube = $linkYoutube;

        return $this;
    }

    public function getLinkDiscord(): ?string
    {
        return $this->linkDiscord;
    }

    public function setLinkDiscord(?string $linkDiscord): self
    {
        $this->linkDiscord = $linkDiscord;

        return $this;
    }

    public function getLinkTwitch(): ?string
    {
        return $this->linkTwitch;
    }

    public function setLinkTwitch(?string $linkTwitch): self
    {
        $this->linkTwitch = $linkTwitch;

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

    /**
     * @return Collection|Group[]
     */
    public function getCreateGroup(): Collection
    {
        return $this->createGroup;
    }

    public function addCreateGroup(Group $createGroup): self
    {
        if (!$this->createGroup->contains($createGroup)) {
            $this->createGroup[] = $createGroup;
            $createGroup->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreateGroup(Group $createGroup): self
    {
        if ($this->createGroup->contains($createGroup)) {
            $this->createGroup->removeElement($createGroup);
            // set the owning side to null (unless already changed)
            if ($createGroup->getCreatedBy() === $this) {
                $createGroup->setCreatedBy(null);
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
            $post->setCeatedBy($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getCeatedBy() === $this) {
                $post->setCeatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getProfilePost(): Collection
    {
        return $this->profilePost;
    }

    public function addProfilePost(Post $profilePost): self
    {
        if (!$this->profilePost->contains($profilePost)) {
            $this->profilePost[] = $profilePost;
            $profilePost->setProfile($this);
        }

        return $this;
    }

    public function removeProfilePost(Post $profilePost): self
    {
        if ($this->profilePost->contains($profilePost)) {
            $this->profilePost->removeElement($profilePost);
            // set the owning side to null (unless already changed)
            if ($profilePost->getProfile() === $this) {
                $profilePost->setProfile(null);
            }
        }

        return $this;
    }
}
