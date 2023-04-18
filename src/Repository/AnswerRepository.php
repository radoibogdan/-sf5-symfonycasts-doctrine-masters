<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * Static parce que
     *  - c'est appelé dans l'entité et on ne peut pas instantier un service à l'intérieur
     *  - on n'utilise pas the $this-> à l'interieur de cette méthode
     * @return Criteria
     */
    public static function getQuestionApprovedCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('status', Answer::STATUS_APPROVED));
    }

    /**
     * @param int $max
     * @return Answer[]
     */
    public function findAMaxNumberOfApprovedAnswers(int $max = 10): array
    {
        return $this->createQueryBuilder('answer')
            ->addCriteria(self::getQuestionApprovedCriteria())
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouver les 10 questions avec le plus de réponses
     * @return Answer[]
     */
    public function findMostPopular() : array
    {
        return $this->createQueryBuilder('answer')
            ->addCriteria(self::getQuestionApprovedCriteria())
            ->orderBy('answer.votes', 'DESC')
            ->setMaxResults(10)
            ->innerJoin('answer.question', 'question')
            ->addSelect('question') # permet de corriger le problème N+1 (app_popular_answers fait un answer.* dans _answer.html.twig)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
