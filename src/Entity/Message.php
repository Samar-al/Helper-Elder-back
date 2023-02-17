<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesSender")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userSenderId;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messagesRecipient")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userRecipientId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUserSenderId(): ?User
    {
        return $this->userSenderId;
    }

    public function setUserSenderId(?User $userSenderId): self
    {
        $this->userSenderId = $userSenderId;

        return $this;
    }

    public function getUserRecipientId(): ?User
    {
        return $this->userRecipientId;
    }

    public function setUserRecipientId(?User $userRecipientId): self
    {
        $this->userRecipientId = $userRecipientId;

        return $this;
    }
}
