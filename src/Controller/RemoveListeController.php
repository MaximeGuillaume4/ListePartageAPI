<?php

namespace App\Controller;

use App\Entity\Liste;
use App\Repository\ListeRepository;
use App\Repository\PartageRepository;
use Symfony\Component\HttpFoundation\Request;

class RemoveListeController
{
    public function __construct(private ListeRepository $listeRepository, private PartageRepository $partageRepository){}

    public function __invoke(Request $request){
        /** @var Liste $liste */
        $liste = $request->attributes->get('data');

        foreach ($liste->getPartages() as $partage){
            $this->partageRepository->remove($partage, true);
        }

        $this->listeRepository->remove($liste, true);

        return 204;
    }


}