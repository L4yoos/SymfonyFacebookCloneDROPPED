<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Like;
use App\Entity\Comment;
use App\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends AbstractController
{
    #[Route('/post/new', name: 'post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());
            $post->setCreatedAt(new \DateTime());
            $post->setUpdatedAt(new \DateTime());

        /** @var UploadedFile[] $attachmentFiles */
        $attachmentFiles = $form->get('attachment')->getData();
        foreach ($attachmentFiles as $attachmentFile) {
            $fileName = md5(uniqid()).'.'.$attachmentFile->guessExtension();

            $attachmentFile->move(
                $this->getParameter('attachment_directory'),
                $fileName
            );

            $post->setAttachment($fileName);
        }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/edit', name: 'post_edit')]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        $this->denyAccessUnlessGranted('edit', $post);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/like', name: 'post_like', methods: ['POST'])]
    public function like(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
    
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }
    
        if ($post->isLikedByUser($user->getId())) {
            $like = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'post' => $post]);
            $post->setLikesCount($post->getLikesCount() - 1);
            $entityManager->remove($like);
            $entityManager->flush();
    
            // return new JsonResponse(['message' => 'Like removed']);
            return $this->redirectToRoute('index');
        }
    
        $like = new Like();
        $like->setUser($user);
        $like->setPost($post);
    
        $post->setLikesCount($post->getLikesCount() + 1);
        $entityManager->persist($like);
        $entityManager->flush();
    
        // return new JsonResponse(['message' => 'Post liked']);
        return $this->redirectToRoute('index');
    }

    #[Route('/post/{id}/comment', name: 'post_comment', methods: ['POST'])]
    public function addComment(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $post = $entityManager->getRepository(Post::class)->find($id);
    
        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }
    
        if (!$post) {
            return new JsonResponse(['message' => 'Post not found'], 404);
        }
    
        $content = $request->request->get('content');
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setAuthor($user);
        $comment->setPost($post);
        $comment->setCreatedAt(new \DateTime());
        $comment->setUpdatedAt(new \DateTime());
    
        $entityManager->persist($comment);
        $entityManager->flush();
    
        return $this->redirectToRoute('index');
    }

    #[Route('/post/{id}', name: 'post_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        $this->denyAccessUnlessGranted('delete', $post);

        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index');
    }
}
