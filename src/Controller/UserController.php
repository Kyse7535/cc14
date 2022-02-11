<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository,  Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $request->getSession()->set('connected', 'true');
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $userAlreadyAnimateur = in_array('ROLE_ANIMATEUR', $user->getRoles());
        $userAlreadyEnfant = in_array('ROLE_ENFANT', $user->getRoles());
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userIsAdmin = in_array('ROLE_ADMIN', $user->getRoles());
            $userIsEnfant = in_array('ROLE_ENFANT', $user->getRoles());
            $userIsAnimateur = in_array('ROLE_ANIMATEUR', $user->getRoles());
            if ($userIsAdmin && $userIsEnfant)
            {
                throw $this->createAccessDeniedException("Un enfant ne peut pas être admin");
            }
            if ($userIsAnimateur && $userIsEnfant)
            {
                throw $this->createAccessDeniedException("Impossible d'être animateur et enfant à la fois");
            }
            if ($userAlreadyAnimateur == true && !$userIsAnimateur) {
                throw $this->createAccessDeniedException("impossible retirer le role d'animateur à un animateur");
            }
            if ($userAlreadyEnfant == true && !$userIsEnfant) {
                throw $this->createAccessDeniedException("impossible retier le role d'enfant à un enfant");
            }
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $userIsAdmin = in_array('ROLE_ADMIN', $user->getRoles());
        if ($userIsAdmin) {
            throw $this->createAccessDeniedException("Impossible de supprimer un admin");
        }
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
