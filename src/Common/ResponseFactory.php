<?php

namespace Common;

use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\ResponseFactory as DiactorosResponseFactory;

class ResponseFactory extends DiactorosResponseFactory
{
    public function createJsonResponse(
        array $data,
        int $status = 200,
        array $headers = []
    ): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    public function createEmptyResponse(int $status = 204, array $headers = []): EmptyResponse
    {
        return new EmptyResponse($status, $headers);
    }
}