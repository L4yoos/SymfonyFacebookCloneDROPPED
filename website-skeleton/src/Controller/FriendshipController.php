<?php
namespace App\Controller;

use App\Entity\FriendShip;
use App\Service\NotificationService;
use App\Form\FriendRequestType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    #[Route('/send-friend-request/{id}', name: 'send_friend_request')]
    public function sendFriendRequest(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $currentUser = $this->getUser();
        
        $userToAdd = $userRepository->find($id);
        
        if (!$userToAdd) {
            throw $this->createNotFoundException('User not found');
        }
    
        $friendship = new Friendship();
        $friendship->setUser($currentUser);
        $friendship->setFriend($userToAdd);
        $friendship->setStatus('pending');
    
        $entityManager->persist($friendship);
        $entityManager->flush();
    
        $notificationService = new NotificationService($entityManager);
        $notificationService->createNotification("friend_request", $userToAdd, null, $currentUser->getId(), $currentUser);

        $this->addFlash('success', 'Zaproszenie do znajomych zostało wysłane.');
        return $this->redirectToRoute('index');
    }    

    #[Route('/accept-friend-request/{id}', name: 'accept_friend_request')]
    public function acceptFriendRequest(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $friendship = $entityManager->getRepository(FriendShip::class)->find($id);

        if (!$friendship) {
            throw $this->createNotFoundException('Friendship invitation not found');
        }

        $friendship->setStatus('accepted');
        $entityManager->flush();

        $reverseFriendship = new Friendship();
        $reverseFriendship->setUser($friendship->getFriend());
        $reverseFriendship->setFriend($friendship->getUser());
        $reverseFriendship->setStatus('accepted');

        $entityManager->persist($reverseFriendship);
        $entityManager->flush();

        $this->addFlash('success', 'Zaproszenie do znajomych zostało zaakceptowane.');
        return $this->redirectToRoute('index');
    }

    #[Route('/remove-friend/{id}', name: 'remove_friend')]
    public function removeFriend(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $friendship = $entityManager->getRepository(FriendShip::class)->find($id);

        if (!$friendship) {
            throw $this->createNotFoundException('Friendship not found');
        }

        $reverseFriendship = $entityManager->getRepository(FriendShip::class)->findOneBy([
            'user' => $friendship->getFriend(),
            'friend' => $friendship->getUser()
        ]);

        if ($reverseFriendship) {
            $entityManager->remove($friendship);
            $entityManager->remove($reverseFriendship);
            $entityManager->flush();

            $this->addFlash('success', 'Znajomy został usunięty.');
        } else {
            $this->addFlash('error', 'Wystąpił błąd podczas usuwania znajomego.');
        }

        return $this->redirectToRoute('index');
    }

    #[Route('/block-friend/{id}', name: 'block_friend')]
    public function blockFriend(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $friendship = $entityManager->getRepository(FriendShip::class)->find($id);

        if (!$friendship) {
            throw $this->createNotFoundException('Friendship not found');
        }

        $reverseFriendship = $entityManager->getRepository(FriendShip::class)->findOneBy([
            'user' => $friendship->getFriend(),
            'friend' => $friendship->getUser()
        ]);

        if ($reverseFriendship) {
            $reverseFriendship->setBlocked(true);
            $friendship->setBlocked(true);
            $entityManager->flush();

            $this->addFlash('success', 'Znajomy został usunięty.');
        } else {
            $this->addFlash('error', 'Wystąpił błąd podczas usuwania znajomego.');
        }

        return $this->redirectToRoute('index');
    }
}
