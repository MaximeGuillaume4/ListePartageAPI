<?php

namespace App\Controller;

use App\Entity\Ligne;
use App\Repository\LigneRepository;
use App\Repository\ListeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DeleteLigneController
{
    public function __construct(private LigneRepository $ligneRepository, private ListeRepository $listeRepository){}

    public function __invoke(Request $request){
        /** @var Ligne $ligneDeleted */
        $ligneDeleted = $request->attributes->get('data');
        $positionDeleted = $ligneDeleted->getPosition();

        $this->ligneRepository->remove($ligneDeleted, true);

        $lignes = $this->ligneRepository->createQueryBuilder('l')
            ->where('l.liste = :current_liste AND l.position > :position_deleted')
            ->setParameter('current_liste', $ligneDeleted->getListe())
            ->setParameter('position_deleted', $positionDeleted)
            ->orderBy('l.position', 'ASC')
            ->getQuery()
            ->execute();


        /** @var Ligne $ligne */
        foreach ($lignes as $ligne){
            $position = $ligne->getPosition();
            $ligne->setPosition($position-1);
            $this->ligneRepository->add($ligne, true);
        }

        return new JsonResponse('Ligne deleted', 204);
    }

}