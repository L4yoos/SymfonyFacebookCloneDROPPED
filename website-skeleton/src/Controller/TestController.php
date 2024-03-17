<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Repository\UserRepository;
use App\Repository\FriendShipRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository, FriendShipRepository $friendShipRepository, PostRepository $postRepository, PersistenceManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();

        $users = $userRepository->findUsersToAddAsFriend($user);

        $friends = $friendShipRepository->findFriends($user->getId());

        $invites = $friendShipRepository->findFriendshipInvitations($user);

        $friendIds = [];
        foreach ($friends as $friendship) {
            $user = $friendship->getUser();
            $friend = $friendship->getFriend();
            
            if ($user->getId() !== $user->getId()) {
                $friendIds[] = $user->getId();
            }
            if ($friend->getId() !== $user->getId()) {
                $friendIds[] = $friend->getId();
            }
        }

        $posts = $postRepository->findPostsByAuthors($friendIds);

        foreach ($posts as $post) {
            $comments = $post->getComments();
            $post->setComments($comments);
        }

        return match($user->isVerified()) {
            true => $this->render('test/index.html.twig', [
                'users' => $users,
                'friends' => $friends,
                'invites' => $invites,
                'posts' => $posts,
            ]),
            false => $this->render('test/please-verify-email.html.twig'),
        };
    }

    #[Route('/search-users', name: 'search_users')]
    public function searchUsers(Request $request, UserRepository $userRepository): Response
    {
        $searchTerm = $request->query->get('searchTerm');
    
        $users = $userRepository->searchUsers($searchTerm);

        return $this->render('test/search.html.twig', [
            'users' => $users,
        ]);
    }
}
