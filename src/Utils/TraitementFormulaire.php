<?php

use Michelf\Markdown;
use Symfony\Component\Form\FormInterface;
use App\Entity\Activite;

class TraitementFormulaire {
    public static function create_an_activite(FormInterface $form): Activite
    {
        $activite = new Activite();
        $nom = htmlspecialchars($form->get('nom')->getData());
        $description = Markdown::defaultTransform($form->get('description')->getData());
        $activite->setNom($nom);
        $activite->setDescription($description);
        return $activite;
    }

    public static function modify_description_of_an_activite(FormInterface $form): string
    {
        $description = Markdown::defaultTransform($form->get('description')->getData());
        return $description;
    }
}