<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Activite;

class AppFixtures extends Fixture
{
    protected $faker;
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        for ($i = 0; $i < 5; $i ++) {
            $activite = new Activite();
            $activite->setNom($this->faker->name());
            $activite->setDescription($this->faker->text());

            $animateur = new User();
            $animateur->setNom($this->faker->firstName());
            $animateur->setPrenom($this->faker->lastName());
            $animateur->setUsername($this->faker->userName());
            $animateur->setPassword($this->faker->password(6,10));
            $animateur->setRoles(['ROLE_ANIMATEUR']);

            for($j = 0; $j < 2; $j ++) {
                $enfant = new User();
                $enfant->setNom($this->faker->firstName());
                $enfant->setPrenom($this->faker->lastName());
                $enfant->setUsername($this->faker->userName());
                $enfant->setPassword($this->faker->password(6,10));
                $enfant->setRoles(['ROLE_ENFANT']);
                $activite->setAnimateur($animateur);
                $activite->addEnfant($enfant);
                $manager->persist($enfant);
            }



            $manager->persist($activite);
            $manager->persist($animateur);

        }
        $manager->flush();
    }
}
