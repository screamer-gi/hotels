<?php

namespace Hotel\Controller;

use Common\DbInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;

class DeleteAction
{
    /** @var ResponseFactory */
    private $responseFactory;

    /** @var DbInterface */
    private $db;

    public function __construct(
        ResponseFactory $responseFactory,
        DbInterface $db
    ) {
        $this->responseFactory = $responseFactory;
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getParsedBody()['id'] ?? null;

        if (!$id || !($interval = $this->db->intervals($id))) {
            return $this->responseFactory->createResponse(404);
        }

        $interval->delete();

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader('Location', '/');
    }
}