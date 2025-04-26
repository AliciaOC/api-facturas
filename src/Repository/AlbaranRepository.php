<?php

namespace App\Repository;

use App\Entity\Albaran;
use App\Entity\LineaAlbaran;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Scalar\MagicConst\Line;

/**
 * @extends ServiceEntityRepository<Albaran>
 */
class AlbaranRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Albaran::class);
    }

    public function guardar(Albaran $albaran): void
    {
        $this->getEntityManager()->persist($albaran);
        $this->getEntityManager()->flush();
    }

    public function borrar(Albaran $albaran): void
    {
        $this->getEntityManager()->remove($albaran);
        $this->getEntityManager()->flush();
    }
}
