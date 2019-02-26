<?php

namespace Hotel\Controller;

use Common\DbInterface;
use Hotel\IntervalHydrator;
use Hotel\IntervalService;
use Hotel\IntervalValidator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

class EditAction
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var PhpRenderer */
    private $renderer;

    /** @var DbInterface */
    private $db;

    /** @var IntervalValidator */
    private $validator;

    /** @var IntervalService */
    private $service;

    /** @var IntervalHydrator */
    private $hydrator;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        PhpRenderer $renderer,
        DbInterface $db,
        IntervalValidator $validator,
        IntervalService $service,
        IntervalHydrator $hydrator
    ) {
        $this->responseFactory = $responseFactory;
        $this->renderer = $renderer;
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

        if ($request->getMethod() == 'POST') {
            $data = $request->getParsedBody();
            if ($this->validator->validate($data)) {
                $this->hydrator->hydrate($interval, $this->validator->getFilteredData());

                if ($this->service->update($interval)) {
                    return $this->responseFactory
                        ->createResponse(302)
                        ->withHeader('Location', '/');
                }

                $this->validator->addError('', 'Error saving interval');
            }
        } else {
            $data = $interval->getData();
        }

        return $this->renderer->render($this->responseFactory->createResponse(), 'hotels/edit.phtml', [
            'action' => 'edit/' . $id,
            'id' => $id,
            'data' => $data,
            'errors' => $this->validator->getErrorSummary(),
        ]);
    }
}