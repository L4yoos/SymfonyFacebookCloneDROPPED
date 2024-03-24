<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createNotification($type, $recipient, $postId = null, $profileId = null, $sender)
    {
        $notification = new Notification();
        $notification->setType($type);
        $notification->setRecipient($recipient);
        $notification->setPostId($postId);
        $notification->setProfileId($profileId);
        $notification->setSender($sender);
        $notification->setCreatedAt(new \DateTime());

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }
}
