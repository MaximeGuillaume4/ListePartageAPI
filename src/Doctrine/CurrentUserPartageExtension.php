<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Liste;
use App\Entity\Partage;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CurrentUserPartageExtension implements QueryItemExtensionInterface
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

        #Create Partage
        if($resourceClass === User::class && $context["request_uri"] === "/api/partages"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.id != :current_user ")
                ->setParameter( 'current_user', $id);
        }
        elseif($resourceClass === Liste::class && $context["request_uri"] === "/api/partages"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere("$alias.user = :current_user")
                ->setParameter( 'current_user',$id);
        }

        #Delete Partage
        elseif ($resourceClass === Partage::class && $operationName === "delete"){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->leftJoin("$alias.liste", "l")
                ->leftJoin("$alias.user", "u")
                ->andWhere("l.user = :current_user OR u.id = :current_user")
                ->setParameter( 'current_user', $id);
        }


    }
}