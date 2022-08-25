<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\ListeRepository;
use App\Repository\PartageRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class DeleteUserController
{
    public function __construct(private Security $security,
                                private UserRepository $userRepository,
                                private ListeRepository $listeRepository,
                                private PartageRepository $partageRepository,
                                private ManagerRegistry $doctrine
    ){}

    public function __invoke(){
        /** @var User $user */
        $user = $this->security->getUser();

        foreach ($user->getListes() as $liste){
            foreach ($liste->getPartages() as $partage){
                $this->partageRepository->remove($partage, true);
            }
            $this->listeRepository->remove($liste, true);
        }
        foreach ($user->getPartages() as $partage){
            $this->partageRepository->remove($partage, true);
        }

        $refresh_token = $this->doctrine->getRepository(RefreshToken::class)->findBy(['username' => $user->getId()]);

        $entityManager = $this->doctrine->getManager();
        foreach ($refresh_token as $r){
            $entityManager->remove($r);
            $entityManager->flush();
        }

        $this->userRepository->remove($user, true);

        return new JsonResponse('User deleted', 204);
    }

}