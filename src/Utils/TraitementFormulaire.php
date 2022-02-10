<?php

use Michelf\Markdown;
use Symfony\Component\Form\FormInterface;
use App\Entity\Activite;
use App\Entity\User;

class TraitementFormulaire {
    public static function create_an_activite(FormInterface $form, User $userConnected): Activite
    {
        $activite = new Activite();
        $nom = htmlspecialchars($form->get('nom')->getData());
        $description = Markdown::defaultTransform($form->get('description')->getData());
        $activite->setNom($nom);
        $activite->setDescription($description);
        $activite->setAnimateur($userConnected);
        return $activite;
    }

    public static function modify_description_of_an_activite(FormInterface $form): string
    {
        $description = Markdown::defaultTransform($form->get('description')->getData());
        return $description;
    }

    public static function isOwner(User $userConnected, Activite $activite)
    {
        return $activite->getAnimateur()->getUsername() === $userConnected->getUsername();
    }
}