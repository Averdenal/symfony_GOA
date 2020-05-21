<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface,\Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"show:comment"})
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
     * @Groups({"show:comment"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"show:comment"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"show:comment"})
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
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="createdBy",cascade={"persist","remove"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="profile")
     */
    private $profilePost;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist"})
     * @Groups({"show:comment"})
     */
    private $pictureProfil;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"})
     */
    private $banner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friends", mappedBy="user1", orphanRemoval=true, cascade={"persist"})
     */
    private $friends;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friends", mappedBy="user2", orphanRemoval=true, cascade={"persist"})
     */
    private $friendsAddMe;

    /**
     * @ORM\OneToMany(targetEntity=AffGroupe::class, mappedBy="user")
     */
    private $affGroupes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visite;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="orga", orphanRemoval=true)
     */
    private $orgaevents;


    public function __construct()
    {
        $this->createGroup = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->profilePost = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->friendsAddMe = new ArrayCollection();
        $this->affGroupes = new ArrayCollection();
        $this->orgaevents = new ArrayCollection();
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
        $roles[] = 'ROLE_NEW';

        return array_unique($roles);
    }
    public function addRole(string $role){
        $this->roles[] = $role;
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
            /* set the owning side to null (unless already changed)
            if ($post->getCreatedBy() === $this) {
                $post->setCreatedBy(null);
            }*/
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

    /**
     * @return Picture|null
     */
    public function getPictureProfil(): ?Picture
    {
        return $this->pictureProfil;
    }

    /**
     * @param Picture|null $pictureProfil
     */
    public function setPictureProfil(?Picture $pictureProfil): self
    {
        if($pictureProfil){
            $this->updatedAt = new DateTime('now');
        }
        $this->pictureProfil = $pictureProfil;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->pseudo,
            $this->password
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->pseudo,
            $this->password
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getBanner(): ?Picture
    {
        return $this->banner;
    }

    public function setBanner(?Picture $banner): self
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * @return Collection|Friends[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(Friends $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
            $friend->setUser1($this);
            $friend->setStatus($friend::statusaff[0]);
        }

        return $this;
    }

    public function removeFriend(Friends $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
            // set the owning side to null (unless already changed)
            if ($friend->getUser1() === $this) {
                $friend->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friends[]
     */
    public function getFriendsAddMe(): Collection
    {
        return $this->friendsAddMe;
    }

    public function removeFriendsAddMe(Friends $friendsAddMe): self
    {
        if ($this->friendsAddMe->contains($friendsAddMe)) {
            $this->friendsAddMe->removeElement($friendsAddMe);
            // set the owning side to null (unless already changed)
            if ($friendsAddMe->getUser2() === $this) {
                $friendsAddMe->setUser2(null);
            }
        }
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
            $affGroupe->setUser($this);
        }

        return $this;
    }

    public function removeAffGroupe(AffGroupe $affGroupe): self
    {
        if ($this->affGroupes->contains($affGroupe)) {
            $this->affGroupes->removeElement($affGroupe);
        }

        return $this;
    }

    public function getVisite(): ?int
    {
        return $this->visite;
    }

    public function setVisite(?int $visite): self
    {
        $this->visite = $visite;

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getOrgaevents(): Collection
    {
        return $this->orgaevents;
    }

    public function addOrgaevent(Event $orgaevent): self
    {
        if (!$this->orgaevents->contains($orgaevent)) {
            $this->orgaevents[] = $orgaevent;
            $orgaevent->setOrga($this);
        }

        return $this;
    }

    public function removeOrgaevent(Event $orgaevent): self
    {
        if ($this->orgaevents->contains($orgaevent)) {
            $this->orgaevents->removeElement($orgaevent);
            // set the owning side to null (unless already changed)
            if ($orgaevent->getOrga() === $this) {
                $orgaevent->setOrga(null);
            }
        }

        return $this;
    }
}
