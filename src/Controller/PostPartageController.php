<?php

namespace App\Controller;

use App\Dto\Request\RegisterPartageRequest;
use App\Entity\Partage;
use App\Repository\PartageRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostPartageController
{

    public function __construct(private PartageRepository $repository){}

    public function __invoke(Request $request){

        /** @var Partage $partage */
        $partage = $request->attributes->get('data');

        $exists = $this->repository->partageExists($partage->getUser()->getValues()[0]->getId(), $partage->getListe()->getValues()[0]->getId());
        if($exists){
            return new JsonResponse('Already Exists', 404);
        }
        else{
            $this->repository->add($partage, true);
            return $partage;
        }
    }
}