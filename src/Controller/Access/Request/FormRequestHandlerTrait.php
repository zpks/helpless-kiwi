<?php

namespace App\Controller\Access\Request;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

trait FormRequestHandlerTrait
{
    private $requestHandler;

    protected function setRequestHandler($requestHandler = 'Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler'): self
    {
        if (\is_string($requestHandler)) {
            $instance = new $requestHandler();
            if ($instance instanceof RequestHandlerInterface) {
                $this->requestHandler = $instance;

                return $this;
            }
        } elseif ($requestHandler instanceof RequestHandlerInterface) {
            $this->requestHandler = $requestHandler;

            return $this;
        }

        // All other cases
        throw new \UnexpectedValueException('$requestHandler must either be a fully-qualified classname or an instance of a class that implements RequestHandlerInterface.');
    }

    protected function getRequestHandler(): RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    protected function handleRequest(FormInterface $form, Request $request): self
    {
        if (null === $this->requestHandler) {
            $this->setRequestHandler();
        }

        $this->requestHandler->handleRequest($form, $request);

        return $this;
    }
}
