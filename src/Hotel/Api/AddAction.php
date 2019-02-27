<?php

namespace Hotel\Api;

use Common\ResponseFactory;
use Hotel\IntervalFactory;
use Hotel\IntervalHydrator;
use Hotel\IntervalService;
use Hotel\IntervalValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AddAction
{
    /** @var ResponseFactory */
    private $responseFactory;

    /** @var IntervalValidator */
    private $validator;

    /** @var IntervalFactory */
    private $factory;

    /** @var IntervalService */
    private $service;

    /** @var IntervalHydrator */
    private $hydrator;

    public function __construct(
        ResponseFactory $responseFactory,
        IntervalValidator $validator,
        IntervalFactory $factory,
        IntervalService $service,
        IntervalHydrator $hydrator
    ) {
        $this->responseFactory = $responseFactory;
        $this->validator = $validator;
        $this->factory = $factory;
        $this->service = $service;
        $this->hydrator = $hydrator;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->validator->validate($request->getParsedBody())) {
            $interval = $this->factory->create();
            $this->hydrator->hydrate($interval, $this->validator->getFilteredData());

            if ($this->service->create($interval)) {
                return $this->responseFactory->createEmptyResponse(201);
            } else {
                return $this->responseFactory->createResponse(500);
            }
        }

        return $this->responseFactory->createJsonResponse(['errors' => $this->validator->getErrors()], 400);
    }
}