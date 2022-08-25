<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CurrentUserExtension implements QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToItem(QueryBuilder $queryBuilder,
                                QueryNameGeneratorInterface $queryNameGenerator,
                                string $resourceClass,
                                array $identifiers,
                                string $operationName = null,
                                array $context = [])
    {
        $id = $this->security->getUser()->getUserIdentifier();
        $groups = $context['groups'] ?? [''];

        #Write user
        if ($resourceClass === User::class && $groups[0] === "read:user:update"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.user = :current_user")
                ->setParameter( 'current_user', $id);
        }
    }
}