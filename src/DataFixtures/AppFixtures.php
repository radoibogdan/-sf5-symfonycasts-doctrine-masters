<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # unpublished change les valeurs par défaut, c'est une méthode créée
        $questions = QuestionFactory::createMany(20);
        QuestionFactory::new()->unpublished()->many(5)->create();

        # Tous les réponses avec la même question
        # AnswerFactory::createMany(100, [
        #     # Récupère une Question aléatoire depuis la bdd, Attention: Il faut quelles soient créées à ce moment
        #     'question' => QuestionFactory::random()
        # ]);

        # Tous les réponses avec question différente
        # AnswerFactory::createMany(100, function () {
        #     return [
        #         # Récupère une Question aléatoire depuis la bdd, Attention: Il faut quelles soient créées à ce moment
        #         'question' => QuestionFactory::random()
        #     ];
        # });

        # Tous les réponses avec question différente mais fait dans Answer factory
        AnswerFactory::createMany(100, function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });

        AnswerFactory::new(
            function () use ($questions) {
                return [
                    'question' => $questions[array_rand($questions)]
                ];
            }
        )->needsApproval()->many(20)->create();
    }
}
