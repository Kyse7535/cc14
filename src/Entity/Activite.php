<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'activites')]
    #[ORM\JoinColumn(nullable: true)]
    private $animateur;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'activitesEnfant')]
    private $enfants;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAnimateur(): ?User
    {
        return $this->animateur;
    }

    public function setAnimateur(?User $animateur): self
    {
        $this->animateur = $animateur;

        return $this;
    }


    public function addEnfant(User $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->addActivitesEnfant($this);
        }

        return $this;
    }



    public function isInscrit(User $userConnected)
    {
        return $this->enfants->contains($userConnected);
    }


    /**
     * @return Collection|User[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function removeEnfant(User $enfant): self
    {
        $this->enfants->removeElement($enfant);

        return $this;
    }
}
