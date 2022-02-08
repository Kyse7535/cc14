<?php

namespace App\DataFixtures;

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
            $manager->persist($activite);
        }
        $manager->flush();
    }
}
