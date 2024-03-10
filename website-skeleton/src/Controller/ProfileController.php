<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'profile')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return match($user->isVerified()) {
            true => $this->render('profile/index.html.twig', [
                'user' => $user,
                'controller_name' => 'ProfileController',
            ]),
            false => $this->render('test/please-verify-email.html.twig'),
        };
    }

    #[Route('/profile/edit/{id}', name: 'profile_edit')]
    public function editProfile(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to edit your profile.');
        }
    
        if ($user->getId() !== $id) {
            throw $this->createAccessDeniedException('You are not authorized to edit this profile.');
        }
    
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Your profile has been updated successfully.');
            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }
    
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
