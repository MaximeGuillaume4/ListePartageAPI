<?php

namespace App\Controller;

use App\Entity\Ligne;
use App\Entity\Liste;
use App\Repository\LigneRepository;
use Symfony\Component\HttpFoundation\Request;

class AddLigneController
{

    public function __construct(private LigneRepository $repository){}

    public function __invoke(Request $request)
    {
        /** @var Liste $liste */
        $liste = $request->attributes->all()["data"];

        /** @var Ligne $ligne */

        $ligne = new Ligne();
        $ligne->setListe($liste);
        $ligne->setPosition($liste->getMaxLignePosition()+1);

        $this->repository->add($ligne, true);

        return $ligne;
    }

}