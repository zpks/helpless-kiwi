<?php

namespace App\Controller\Access;

use App\Controller\Access\Request\FormRequestHandlerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Entity access.
 */
abstract class AbstractAccess extends AbstractController
{
    const SUCCESS = 0;
    const NOTSUBMITTED = 1;
    const INVALID = 2;

    use FormRequestHandlerTrait;
}
