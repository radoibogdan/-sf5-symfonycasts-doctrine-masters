<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return Question[] Returns an array of Question objects
     */
    public function findAllAskedOrderedByNewest()
    {
        # Simple ManyToMany
        # return $this->addIsAskedQueryBuilder()
        #     ->orderBy('q.askedAt', 'DESC')
        #     ->leftJoin('q.tags', 'tag') # ManyToMany, tag = alias for the Tag Table
        #     ->addSelect('tag')              # Fix N + 1 problem on homepage, Select all data from Tag table
        #     ->getQuery()
        #     ->getResult()
        # ;

        # Complexe ManyToMany avec colonne en plus dans la table question_tag
        return $this->addIsAskedQueryBuilder()
            ->orderBy('q.askedAt', 'DESC')
            ->leftJoin('q.questionTags', 'question_tag') # alias for the QuestionTag Table
            ->innerJoin('question_tag.tag', 'tag')       # ManyToMany, tag = alias for the Tag Table
            # il faut rajouteur aussi la table d'association questionTag si on veut les données de la tables Tag
            ->addSelect(['question_tag', 'tag'])                   # Fix N + 1 problem on homepage, Select all data from Tag table
            ->getQuery()
            ->getResult()
            ;
    }

    private function addIsAskedQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('q.askedAt IS NOT NULL');
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('q');
    }

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
