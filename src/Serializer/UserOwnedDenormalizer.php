<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{

    use DenormalizerAwareTrait;
    private const ALREADY_CALLED_DENORMALIZER = "UserOwnedDenormalizerCalled";

    public function __construct(private Security $security){}

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        $alreadyCalled = $context[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        $groups = $context['groups'] ?? [''];

        if($alreadyCalled === true){
            return false;
        }
        elseif($type === "App\Entity\User" && $groups[0] === 'write:user:update'){
            return true;
        }
        else{
            return false;
        }
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_DENORMALIZER] = true;

        /** @var User $obj */
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
        #$obj->setId($this->security->getUser()->getUserIdentifier());


        return $obj;


    }

}