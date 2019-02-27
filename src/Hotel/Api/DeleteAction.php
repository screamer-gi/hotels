<?php

namespace Hotel\Api;

use Common\DbInterface;
use Exception;
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
        $id = $request->getAttribute('id');

        if (!$id || !($interval = $this->db->intervals($id))) {
            return $this->responseFactory->createResponse(404);
        }

        try {
            $interval->delete();
            return $this->responseFactory->createResponse(204);
        } catch (Exception $e) {
            return $this->responseFactory->createResponse(500);
        }
    }
}