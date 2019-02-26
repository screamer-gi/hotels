<?php

namespace Hotel\Controller;

use Common\DbInterface;
use Hotel\IntervalFactory;
use Hotel\IntervalHydrator;
use Hotel\IntervalService;
use Hotel\IntervalValidator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class AddAction
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var PhpRenderer */
    private $renderer;

    /** @var DbInterface */
    private $db;

    /** @var IntervalValidator */
    private $validator;

    /** @var IntervalFactory */
    private $factory;

    /** @var IntervalService */
    private $service;

    /** @var IntervalHydrator */
    private $hydrator;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        PhpRenderer $renderer,
        DbInterface $db,
        IntervalValidator $validator,
        IntervalFactory $factory,
        IntervalService $service,
        IntervalHydrator $hydrator
    ) {
        $this->responseFactory = $responseFactory;
        $this->renderer = $renderer;
        $this->db = $db;
        $this->validator = $validator;
        $this->factory = $factory;
        $this->service = $service;
        $this->hydrator = $hydrator;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() == 'POST') {
            if ($this->validator->validate($request->getParsedBody())) {
                $interval = $this->factory->create();
                $this->hydrator->hydrate($interval, $this->validator->getFilteredData());

                if ($this->service->create($interval)) {
                    return $this->responseFactory
                        ->createResponse(302)
                        ->withHeader('Location', '/');

                }

                $this->validator->addError('', 'Error saving interval');
            }
        }

        return $this->renderer->render($this->responseFactory->createResponse(), 'hotels/add.phtml', [
            'action' => 'add',
            'data' => $request->getParsedBody(),
            'errors' => $this->validator->getErrorSummary(),
        ]);
    }
}