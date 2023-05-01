<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    private $logger;
    private $isDebug;

    public function __construct(LoggerInterface $logger, bool $isDebug)
    {
        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }


    /**
     * Match uniquement les routes qui sont des chiffres ex: /2 /3
     *
     * @Route("/{page<\d+>}", name="app_homepage")
     */
    public function homepage(QuestionRepository $questionRepository, Request $request, int $page = 1)
    {
//        $questions = $questionRepository->findBy(
//            [], # get all
//            ['askedAt' => 'DESC'] # order by column
//        );

        # With Paginator
        # Custom query
        $queryBuilder = $questionRepository->createAskedOrderedByNewestQueryBuilder();

        $pagerFanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );
        $pagerFanta->setMaxPerPage(5); // doit être avant le set current page

        # Récupérer page depuis url
        $pagerFanta->setCurrentPage($page);

        # Récupérer page depuis Request
        # $pagerFanta->setCurrentPage($request->query->get('page', 1)); #default 1 if no `?page=` in url

        return $this->render('question/homepage.html.twig', [
            'pager' => $pagerFanta
        ]);

        # No Paginator
        # Custom query
        # $questions = $questionRepository->findAllAskedOrderedByNewest();
        #
        # return $this->render('question/homepage.html.twig', [
        #     'questions' => $questions
        # ]);
    }


    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager)
    {
        return new Response('V2 to do');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show(Question $question)
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode!');
        }

//        $repository = $entityManager->getRepository(Question::class);
//        /** @var Question $question */
//        $question = $repository->findOneBy(['slug' => $slug]);
//        if (!$question) {
//            // Cette erreur ne sera visible que dans l'environnement de DEV
//            throw $this->createNotFoundException(sprintf("La question after le slug %s n'existe pas", $slug));
//        }

        # Récupérer les Réponses en utilisant l'entité
        # $answers = $answerRepository->findBy(['question' => $question]);

        # Récupérer les Réponses
        $answers = $question->getAnswers();

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods={"POST"})
     */
    public function questionVote(Request $request, Question $question, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');
        if ($direction === 'up') {
            $question->upVote();
        } elseif($direction === 'down') {
            $question->downVote();
        }
        $entityManager->flush();

        return $this->redirectToRoute('app_question_show', [
            'slug' => $question->getSlug()
        ]);
    }
}
