<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\FriendShipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class TestController extends AbstractController
{
    #[Route('/test', name: 'index')]
    public function index(UserRepository $userRepository, FriendShipRepository $friendShipRepository, PersistenceManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();

        $users = $userRepository->findUsersToAddAsFriend($user);

        $friends = $friendShipRepository->findFriends($user->getId());

        $invites = $friendShipRepository->findFriendshipInvitations($user);

        return match($user->isVerified()) {
            true => $this->render('test/index.html.twig', [
                'users' => $users,
                'friends' => $friends,
                'invites' => $invites,
            ]),
            false => $this->render('test/please-verify-email.html.twig'),
        };
    }
}
