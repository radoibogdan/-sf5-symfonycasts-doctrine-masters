<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use App\Factory\QuestionTagFactory;
use App\Factory\TagFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # Crée 100 Tags
        TagFactory::createMany(100);

        # Crée 20 Questions
        $questions = QuestionFactory::createMany(20);

        # Crée 100 QuestionsTags - Complex ManyToMany relation avec extra colonne dans la table
        QuestionTagFactory::createMany(100, function () {
            return [
                'tag' => TagFactory::random(),
                'question' => QuestionFactory::random(),
            ];
        });

        # Crée 5 Questions
        # unpublished change les valeurs par défaut, c'est une méthode créée
        QuestionFactory::new()->unpublished()->many(5)->create();

        # Crée 100 Réponses
        # Tous les réponses avec question différente mais fait dans Answer factory
        AnswerFactory::createMany(100, function () use ($questions) {
            return [
                'question' => $questions[array_rand($questions)]
            ];
        });

        # Crée 20 Réponses
        # Avec statut différent, needsApproval() méthode créée
        AnswerFactory::new(
            function () use ($questions) {
                return [
                    'question' => $questions[array_rand($questions)]
                ];
            }
        )->needsApproval()->many(20)->create();

        # Crée 20 questions - Simple ManyToMany relation
        # Callback, si non renvoie le même nb de tags pour toutes les 20 questions
        # $questions = QuestionFactory::createMany(20, function () {
        #     return [
        #         # récupère entre 0 et 5 tags depuis les 100 tags crées précedement
        #         'tags' => TagFactory::randomRange(0, 5),
        #     ];
        # });

        # Crée 20 questions - Complex ManyToMany relation avec extra colonne dans la table
        # Callback, si non renvoie le même nb de tags pour toutes les 20 questions
        # $questions = QuestionFactory::createMany(20, function () {
        #     return [
        #         # Crée entre 1 et 5 QuestionTags pour chaque question
        #         'questionTags' => QuestionTagFactory::new(
        #             # Callback pour utiliser des tags différents pour chaque QuestionTag
        #             function () {
        #                 return [
        #                     # Récupère un Tag aléatoire depuis ceux créés précédemment
        #                     'tag' => TagFactory::random(),
        #                 ];
        #             }
        #         )->many(1, 5),
        #     ];
        # });

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

        // Crée 1 Question avec 2 Tags : ManyToMany relation manually
        # /** @var Question $question */
        # $question = QuestionFactory::createOne()->object();
        # $tag1 = new Tag();
        # $tag1->setName('dinosaurs');
        # $tag2 = new Tag();
        # $tag2->setName('monsters');

        # $question->addTag($tag1);
        # $question->addTag($tag2);

        # $manager->persist($tag1);
        # $manager->persist($tag2);
        # $manager->flush();
    }
}
