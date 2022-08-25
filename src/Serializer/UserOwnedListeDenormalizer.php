<?php

namespace App\Serializer;

use App\Entity\Liste;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedListeDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{

    use DenormalizerAwareTrait;
    private const ALREADY_CALLED_DENORMALIZER = "UserOwnedListeDenormalizerCalled";

    public function __construct(private Security $security){}

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
       $alreadyCalled = $context[self::ALREADY_CALLED_DENORMALIZER] ?? false;
       $groups = $context['groups'] ?? [''];

       if($alreadyCalled === true){
           return false;
       }
       elseif($type === "App\Entity\Liste" && $groups[0] === 'write:liste:new'){
           return true;
       }
       else{
           return false;
       }
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED_DENORMALIZER] = true;

        /** @var Liste $obj */
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
        $obj->setUser($this->security->getUser());


        return $obj;


    }

}