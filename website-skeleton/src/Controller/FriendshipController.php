<?php
namespace App\Controller;

use App\Entity\FriendShip;
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
        // Pobierz aktualnie zalogowanego użytkownika
        $currentUser = $this->getUser();
        
        // Pobierz użytkownika, któremu ma być wysłane zaproszenie
        $userToAdd = $userRepository->find($id);
        
        // Sprawdź czy użytkownik istnieje
        if (!$userToAdd) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Utwórz nowe zaproszenie do znajomych
        $friendship = new Friendship();
        $friendship->setUser($currentUser);
        $friendship->setFriend($userToAdd);
        $friendship->setStatus('pending');
    
        // Zapisz zaproszenie do bazy danych
        $entityManager->persist($friendship);
        $entityManager->flush();
    
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

        $this->addFlash('success', 'Zaproszenie do znajomych zostało zaakceptowane.');
        return $this->redirectToRoute('index');
    }
}
