<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # unpublished change les valeurs par défaut, c'est une méthode créée
        QuestionFactory::createMany(20);
        QuestionFactory::new()->unpublished()->many(5)->create();
    }
}
