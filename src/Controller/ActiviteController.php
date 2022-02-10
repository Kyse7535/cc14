<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Repository\ActiviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\TraitementFormulaire;
use App\Entity\User;
#[Route('/activite')]
class ActiviteController extends AbstractController
{
    #[Route('/', name: 'activite_index', methods: ['GET'])]
    public function index(ActiviteRepository $activiteRepository, Request $request): Response
    {
        $userConnected = $this->getUser();
        if ($userConnected == null)
        {
            return $this->render('activite/index.html.twig', [
                'activites' => $activiteRepository->findAll(),
                'connected' => true,
                'userConnectedIsAnimateur' => true
            ]);
        }
        $userConnectedIsAnimateur = $userConnected->isAnimateur();
        return $this->render('activite/index.html.twig', [
            'activites' => $activiteRepository->findAll(),
            'connected' => true,
            'userConnectedIsAnimateur' => $userConnectedIsAnimateur
        ]);

    }

    #[Route('/new', name: 'activite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ANIMATEUR');
        $activite = new Activite();
        $form = $this->createFormBuilder($activite)
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $activite = \TraitementFormulaire::create_an_activite($form, $currentUser);
            $entityManager->persist($activite);
            $entityManager->flush();
            $request->getSession()->getFlashBag()->add('create_success', 'Votre activité a bien été enregistrée');

            return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'activite_show', methods: ['GET'])]
    public function show(Activite $activite): Response
    {
        $userConnected = $this->getUser();
        if ($userConnected == null) {
            return $this->render('activite/show.html.twig', [
                'activite' => $activite,
                'userIsConnected' => false,
                'userConnectedIsAnimateur' => false
            ]);
        }
        $userConnectedIsInscrit = $activite->isInscrit($userConnected);
        $userConnectedIsAnimateur = $userConnected->isAnimateur();
        return $this->render('activite/show.html.twig', [
            'activite' => $activite,
            'userIsConnected' => true,
            'userConnectedIsInscrit' => $userConnectedIsInscrit,
            'userConnectedIsAnimateur' => $userConnectedIsAnimateur
        ]);
    }

    #[Route('/{id}/edit', name: 'activite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ANIMATEUR');
        $userConnected = $this->getUser();
        if (!\TraitementFormulaire::isOwner($userConnected, $activite))
        {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activite->setDescription(\TraitementFormulaire::modify_description_of_an_activite($form));
            $entityManager->flush();
            $request->getSession()->getFlashBag()->add('modify_success', 'Votre activité a bien été modifiée');
            return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'activite_delete', methods: ['POST'])]
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ANIMATEUR');
        $userConnected = $this->getUser();
        if (!\TraitementFormulaire::isOwner($userConnected, $activite))
        {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$activite->getId(), $request->request->get('_token'))) {
            $request->getSession()->getFlashBag()->add('delete_success', 'Votre activité a bien été supprimée');
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/inscription', name: 'activite_inscription', methods: ['GET'])]
    public function inscription(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ENFANT', );
        $userConnected =  $this->getUser();
        if (!$userConnected->isAnimateur())
        {
            $activite->addEnfant($userConnected);
            $entityManager->flush();
        }
        return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/desinscription', name: 'activite_desinscription', methods: ['GET'])]
    public function desinscription(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ENFANT');
        $userConnected =  $this->getUser();
        if (!$userConnected->isAnimateur())
        {
            $activite->removeEnfant($userConnected);
            $entityManager->flush();
        }
        return $this->redirectToRoute('activite_index', [], Response::HTTP_SEE_OTHER);
    }
}
