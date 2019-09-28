<?php

namespace App\Access;

use App\Access\Request\FormRequestHandlerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Access\Normalizer\EntityNormalizer;

/**
 * Entity access.
 */
abstract class AbstractAccess extends AbstractController
{
    const SUCCESS = 0;
    const NOTSUBMITTED = 1;
    const INVALID = 2;

    use FormRequestHandlerTrait;

    private $accessBuilder;

    public function __construct(EntityAccessBuilder $accessBuilder)
    {
        $this->accessBuilder = $accessBuilder;
    }

    protected function generateAccess($entity, int $entityDepth = 1, array $blacklist = [], array $whitelist = null): array
    {
        $serializer = new Serializer([new EntityNormalizer($this->accessBuilder), new ObjectNormalizer()], []);

        return $serializer->normalize($entity, null, [ObjectNormalizer::ENABLE_MAX_DEPTH => true]);
    }
}
