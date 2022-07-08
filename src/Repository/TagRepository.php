<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @param string $tagName
     *
     * @return Tag | null
     *
     * @throws NonUniqueResultException
     */
    public function findByName(string $tagName): ?Tag
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(Tag::class, 't')
            ->where('LOWER(t.name) = LOWER(:name)')
            ->setParameter('name', $tagName)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $tagName
     *
     * @return Tag
     *
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function getByName(string $tagName): Tag
    {
        $tag = $this->findByName($tagName);

        if (is_null($tag)) {
            throw new Exception(sprintf('Тег с именем %s не найден', $tagName));
        }

        return $tag;
    }
}
