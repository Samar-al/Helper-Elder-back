<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
class Review
{
    /**
     * @ORM\Id
     * @Groups({"users", "reviews"})
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"users", "reviews"})
     * 
     * @Assert\Length(min = 20, max = 150)
     * @Assert\Type("string")
     */
    private $content;

    /**
     * @ORM\Column(type="smallint")
     * @Groups({"users", "reviews"})
     * @Assert\NotBlank
     * @Assert\Type("integer")
     */
    private $rate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"reviews"})
     */
    private $userGiver;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reviewsTaker")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"reviews"})
     */
    private $userTaker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

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

    public function getUserGiver(): ?User
    {
        return $this->userGiver;
    }

    public function setUserGiver(?User $userGiver): self
    {
        $this->userGiver = $userGiver;

        return $this;
    }

    public function getUserTaker(): ?User
    {
        return $this->userTaker;
    }

    public function setUserTaker(?User $userTaker): self
    {
        $this->userTaker = $userTaker;

        return $this;
    }
}
