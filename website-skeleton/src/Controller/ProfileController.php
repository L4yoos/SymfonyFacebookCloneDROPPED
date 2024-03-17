<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\FriendShipRepository;
use App\Form\ProfileFormType;
use App\Form\ProfilePictureFormType;
use App\Form\CoverPhotoFormType;
use App\Form\UserSearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'profile')]
    public function index(int $id, FriendShipRepository $friendShipRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $entityManager->getRepository(User::class)->find($id);

        $friends = $friendShipRepository->findFriends($user->getId());

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return match($user->isVerified()) {
            true => $this->render('profile/index.html.twig', [
                'user' => $user,
                'friends' => $friends,
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

    #[Route('/profile/upload-profile-picture/{id}', name: 'upload_profile_picture')]
    public function uploadProfilePicture(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to upload a profile picture.');
        }

        $form = $this->createForm(ProfilePictureFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePictureFile = $form->get('profilePicture')->getData();

            // Jeśli użytkownik wybrał plik
            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                
                // Utwórz unikalną nazwę pliku
                $newFilename = $originalFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();

                try {
                    // Przenieś plik do katalogu, gdzie będą przechowywane profile pictures
                    $profilePictureFile->move(
                        $this->getParameter('profile_pictures_directory'),
                        $newFilename
                    );

                    // Zapisz nazwę pliku do encji użytkownika
                    $user->setProfilePictureFilename($newFilename);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Your profile picture has been uploaded successfully.');
                } catch (FileException $e) {
                    // Obsłuż wyjątek, jeśli wystąpił problem z przesyłaniem pliku
                    $this->addFlash('error', 'An error occurred while uploading your profile picture.');
                }
            }

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/upload_profile_picture.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/upload-cover-photo/{id}', name: 'upload_cover_photo')]
    public function uploadCoverPhoto(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to upload a cover photo.');
        }

        $form = $this->createForm(CoverPhotoFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverPhotoFile = $form->get('coverPhoto')->getData();

            // Jeśli użytkownik wybrał plik
            if ($coverPhotoFile) {
                $originalFilename = pathinfo($coverPhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                
                // Utwórz unikalną nazwę pliku
                $newFilename = $originalFilename.'-'.uniqid().'.'.$coverPhotoFile->guessExtension();

                try {
                    // Przenieś plik do katalogu, gdzie będą przechowywane cover photos
                    $coverPhotoFile->move(
                        $this->getParameter('cover_photos_directory'),
                        $newFilename
                    );

                    // Zapisz nazwę pliku do encji użytkownika
                    $user->setCoverPhotoFilename($newFilename);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Your cover photo has been uploaded successfully.');
                } catch (FileException $e) {
                    // Obsłuż wyjątek, jeśli wystąpił problem z przesyłaniem pliku
                    $this->addFlash('error', 'An error occurred while uploading your cover photo.');
                }
            }

            return $this->redirectToRoute('profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/upload_cover_photo.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
