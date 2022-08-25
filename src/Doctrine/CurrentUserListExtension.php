<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Liste;
use App\Entity\Partage;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CurrentUserListExtension implements QueryItemExtensionInterface
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

        #read User owned liste
        if ($resourceClass === Liste::class && $groups[0] === "read:liste:user"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.user = :current_user")
                ->setParameter( 'current_user',$id);
        }

        #read liste from partage
        if ($resourceClass === Liste::class && $groups[0] === "read:liste:partage"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->leftJoin("$alias.partages", "p", )
                ->andWhere(":current_user MEMBER OF p.user")
                ->setParameter( 'current_user',$id);
        }

        #Modify Name Liste
        elseif ($resourceClass === Liste::class && $groups[0] === "write:liste:name"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.user = :current_user")
                ->setParameter( 'current_user',$id);
        }
        elseif ($resourceClass === Liste::class && $groups[0] === "delete:liste"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.user = :current_user")
                ->setParameter( 'current_user',$id);
        }

    }
}