<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationController extends AbstractController
{
    #[Route('/notifications', name: 'notifications')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $notifications = $entityManager->getRepository(Notification::class)->findBy([
            'recipient' => $this->getUser(),
            'isRead' => false,
        ]);

        $response = [];
        foreach ($notifications as $notification) {
            $message = $this->generateMessage($notification);
            
            $response[] = [
                'message' => $message,
                'link' => $notification->getPostId() ? "post/{$notification->getPostId()}" : ($notification->getProfileId() ? "profile/{$notification->getProfileId()}" : ''),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
            ];

            // $notification->setIsRead(true);
        }

        $entityManager->flush();

        return new JsonResponse($response);
    }

    private function generateMessage(Notification $notification): string
    {
        switch ($notification->getType()) {
            case Notification::TYPE_LIKE:
                return $notification->getSender()->getFullname() . ' polubił twój post.';
            case Notification::TYPE_COMMENT:
                return $notification->getSender()->getFullname() . ' skomentował twój post.';
            case Notification::TYPE_FRIEND_REQUEST:
                return $notification->getSender()->getFullname() . ' wysłał(a) Ci zaproszenie do znajomych.';
            default:
                return '';
        }
    }
}
