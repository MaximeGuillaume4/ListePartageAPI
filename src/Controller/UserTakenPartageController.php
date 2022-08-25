<?php

namespace App\Controller;

use App\Entity\Ligne;
use App\Repository\LigneRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class UserTakenPartageController
{
    public function __construct(private Security $security, private LigneRepository $repository){}

    public function __invoke(Request $request): Ligne
    {
        $user = $this->security->getUser();
        /** @var Ligne $ligne */
        $ligne = $request->attributes->get('data');
        if($ligne->getUserTaken() === null){
            $ligne->setUserTaken($user);
            $this->repository->add($ligne, true);
        }
        elseif($ligne->getUserTaken() === $user){
            $ligne->setUserTaken(null);
            $this->repository->add($ligne, true);
        }
        return $ligne;
    }
}