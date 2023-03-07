<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"posts","users", "messages", "reviews"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"posts","users", "reviews"})
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * 
     * @Groups({"messages", "reviews"})
     * 
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"posts","users"})
     * 
     */
    private $password;

    /**
     * 
     * @ORM\Column(type="string", length=64)
     * @Groups({"posts","users", "messages", "reviews"})
     * @Assert\NotBlank
     * @Assert\Length(min = 1, max = 60)
     * @Assert\Type("string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"posts","users", "messages", "reviews"})
     * @Assert\NotBlank
     * @Assert\Length(min = 1, max = 60)
     * 
     */
    private $lastname;

    /**
     * @ORM\Column(type="date")
     * @Groups({"posts","users"})
     *
     */
    private $birthdate;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"posts","users"})
     * 
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=6)
     * @Groups({"posts","users"})
     * @Assert\Length(min = 5, max = 5)
     * @Assert\Type("string")
     */
    private $postalCode;


    /**
     * @ORM\Column(type="text")
     * @Groups({"posts","users"})
     * @Assert\Type("string")
     * @Assert\Length(min = 100, max = 500)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"posts","users"})
     */
    private $avgRating=0;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user")
     * @Groups({"users"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="userGiver")
     * @Groups({"posts","users"})
     */
    private $reviewsGiver;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="userTaker")
     * @Groups({"posts","users"})
     */
    private $reviewsTaker;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="userSender")
     * @Groups({"posts","users",})
     */
    private $messagesSender;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="userRecipient")
     * @Groups({"posts","users"})
     */
    private $messagesRecipient;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="user1")
     * @Groups({"posts","users"})
     */
    private $conversationsUser1;

    /**
     * @ORM\OneToMany(targetEntity=Conversation::class, mappedBy="user2")
     * @Groups({"posts","users"})
     */
    private $conversationsUser2;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"posts","users"})
     * 
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"posts","users", "reviews"})
     */
    private $picture;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

        public function __toString(): string
    {
        return $this->getId();
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->reviewsGiver = new ArrayCollection();
        $this->reviewsTaker = new ArrayCollection();
        $this->messagesSender = new ArrayCollection();
        $this->messagesRecipient = new ArrayCollection();
        $this->conversationsUser1 = new ArrayCollection();
        $this->conversationsUser2 = new ArrayCollection();
    }

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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postalCode = $postal_code;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvgRating(): ?float
    {
        return $this->avgRating;
    }

    public function setAvgRating(?float $avg_rating): self
    {
        $this->avgRating = $avg_rating;

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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviewsGiver(): Collection
    {
        return $this->reviewsGiver;
    }

    public function addReviewGiver(Review $reviewsGiver): self
    {
        if (!$this->reviewsGiver->contains($reviewsGiver)) {
            $this->reviewsGiver[] = $reviewsGiver;
            $reviewsGiver->setUserGiver($this);
        }

        return $this;
    }

    public function removeReviewGiver(Review $review): self
    {
        if ($this->reviewsGiver->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUserGiver() === $this) {
                $review->setUserGiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviewsTaker(): Collection
    {
        return $this->reviewsTaker ?? new ArrayCollection();
    }

    public function addReviewsTaker(Review $reviewsTaker): self
    {
        if (!$this->reviewsTaker->contains($reviewsTaker)) {
            $this->reviewsTaker[] = $reviewsTaker;
            $reviewsTaker->setUserTaker($this);
        }

        return $this;
    }

    public function removeReviewsTaker(Review $reviewsTaker): self
    {
        if ($this->reviewsTaker->removeElement($reviewsTaker)) {
            // set the owning side to null (unless already changed)
            if ($reviewsTaker->getUserTaker() === $this) {
                $reviewsTaker->setUserTaker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesSender(): Collection
    {
        return $this->messagesSender;
    }

    public function addMessagesSender(Message $messagesSender): self
    {
        if (!$this->messagesSender->contains($messagesSender)) {
            $this->messagesSender[] = $messagesSender;
            $messagesSender->setUserSender($this);
        }

        return $this;
    }

    public function removeMessagesSender(Message $messagesSender): self
    {
        if ($this->messagesSender->removeElement($messagesSender)) {
            // set the owning side to null (unless already changed)
            if ($messagesSender->getUserSender() === $this) {
                $messagesSender->setUserSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesRecipient(): Collection
    {
        return $this->messagesRecipient;
    }

    public function addMessagesRecipient(Message $messagesRecipient): self
    {
        if (!$this->messagesRecipient->contains($messagesRecipient)) {
            $this->messagesRecipient[] = $messagesRecipient;
            $messagesRecipient->setUserRecipient($this);
        }

        return $this;
    }

    public function removeMessagesRecipient(Message $messagesRecipient): self
    {
        if ($this->messagesRecipient->removeElement($messagesRecipient)) {
            // set the owning side to null (unless already changed)
            if ($messagesRecipient->getUserRecipient() === $this) {
                $messagesRecipient->setUserRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsUser1(): Collection
    {
        return $this->conversationsUser1;
    }

    public function addConversationsUser1(Conversation $conversationsUser1): self
    {
        if (!$this->conversationsUser1->contains($conversationsUser1)) {
            $this->conversationsUser1[] = $conversationsUser1;
            $conversationsUser1->setUser1($this);
        }

        return $this;
    }

    public function removeConversationsUser1(Conversation $conversationsUser1): self
    {
        if ($this->conversationsUser1->removeElement($conversationsUser1)) {
            // set the owning side to null (unless already changed)
            if ($conversationsUser1->getUser1() === $this) {
                $conversationsUser1->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsUser2(): Collection
    {
        return $this->conversationsUser2;
    }

    public function addConversationsUser2(Conversation $conversationsUser2): self
    {
        if (!$this->conversationsUser2->contains($conversationsUser2)) {
            $this->conversationsUser2[] = $conversationsUser2;
            $conversationsUser2->setUser2($this);
        }

        return $this;
    }

    public function removeConversationsUser2(Conversation $conversationsUser2): self
    {
        if ($this->conversationsUser2->removeElement($conversationsUser2)) {
            // set the owning side to null (unless already changed)
            if ($conversationsUser2->getUser2() === $this) {
                $conversationsUser2->setUser2(null);
            }
        }

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
