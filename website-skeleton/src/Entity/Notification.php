<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    const TYPE_LIKE = 'like';
    const TYPE_COMMENT = 'comment';
    const TYPE_FRIEND_REQUEST = 'friend_request';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $type;

    #[ORM\Column(type: 'boolean')]
    private ?bool $isRead = false;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $recipient;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $sender;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $postId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $profileId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

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

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getPostId(): ?int
    {
        return $this->postId;
    }

    public function setPostId(?int $postId): self
    {
        $this->postId = $postId;

        return $this;
    }

    public function getProfileId(): ?int
    {
        return $this->profileId;
    }

    public function setProfileId(?int $profileId): self
    {
        $this->profileId = $profileId;

        return $this;
    }
}
