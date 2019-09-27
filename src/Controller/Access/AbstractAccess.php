<?php

namespace App\Controller\Access;

use App\Controller\Access\Request\FormRequestHandlerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Controller\Access\Normalizer\EntityNormalizer;

/**
 * Entity access.
 */
abstract class AbstractAccess extends AbstractController
{
    const SUCCESS = 0;
    const NOTSUBMITTED = 1;
    const INVALID = 2;

    use FormRequestHandlerTrait;

    protected function generateAccess($entity, int $entityDepth = 1, array $blacklist = [], array $whitelist = null): array
    {
        $em = $this->getDoctrine()->getManager();

        $normalizer = new EntityNormalizer($this->getDoctrine()->getManager());
        $serializer = new Serializer([$normalizer], []);

        return $serializer->normalize($entity, null, [ObjectNormalizer::ENABLE_MAX_DEPTH => true]);
    }
}
