<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Ligne;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CurrentUserLigneExtension implements QueryItemExtensionInterface
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

        #Modify ligne
        if ($resourceClass === Ligne::class && $groups[0] === 'read:ligne:user'){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->leftJoin("$alias.liste", 'l')
                ->andWhere("l.user = :current_user")
                ->setParameter( 'current_user',$id);
        }

        elseif($resourceClass === Ligne::class && $groups[0] === 'read:ligne:user_taken'){
            $alias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->leftJoin("$alias.liste", 'l')
                ->leftJoin('l.partages', 'p')
                ->andWhere(":current_user MEMBER OF p.user")
                ->andWhere("$alias.user_taken is null OR $alias.user_taken = :current_user")
                ->setParameter( 'current_user',$id);
        }
    }
}