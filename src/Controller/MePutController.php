<?php

namespace App\Controller;

use App\Entity\User;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

class MePutController
{
    public function __construct(private Security $security, private SerializerInterface $serializer, private UserRepository $userRepository){}

    public function __invoke(Request $request): User
    {
        /** @var User $dto */
        $dto = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        /** @var User $user */
        $user = $this->security->getUser();
        if($dto->getUsername()){
            $user->setUsername($dto->getUsername());
        }
        if($dto->getEmail()){
            $user->setEmail($dto->getEmail());
        }

        $this->userRepository->add($user, true);
        return $user;
    }
}