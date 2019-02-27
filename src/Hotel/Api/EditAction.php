<?php

namespace Hotel\Api;

use Common\DbInterface;
use Common\ResponseFactory;
use Hotel\IntervalHydrator;
use Hotel\IntervalService;
use Hotel\IntervalValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EditAction
{
    /** @var ResponseFactory */
    private $responseFactory;

    /** @var DbInterface */
    private $db;

    /** @var IntervalValidator */
    private $validator;

    /** @var IntervalService */
    private $service;

    /** @var IntervalHydrator */
    private $hydrator;

    public function __construct(
        ResponseFactory $responseFactory,
        DbInterface $db,
        IntervalValidator $validator,
        IntervalService $service,
        IntervalHydrator $hydrator
    ) {
        $this->responseFactory = $responseFactory;
        $this->db = $db;
        $this->validator = $validator;
        $this->service = $service;
        $this->hydrator = $hydrator;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        if (!$id || !($interval = $this->db->intervals($id))) {
            return $this->responseFactory->createResponse(404);
        }

        if ($this->validator->validate($request->getParsedBody())) {
            $this->hydrator->hydrate($interval, $this->validator->getFilteredData());

            if ($this->service->update($interval)) {
                return $this->responseFactory->createEmptyResponse();
            } else {
                return $this->responseFactory->createResponse(500);
            }
        }

        return $this->responseFactory->createJsonResponse(['errors' => $this->validator->getErrors()], 400);
    }
}