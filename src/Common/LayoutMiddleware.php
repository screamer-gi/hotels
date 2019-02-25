<?php

namespace Common;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\PhpRenderer;

class LayoutMiddleware implements MiddlewareInterface
{
    /** @var PhpRenderer */
    private $renderer;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(PhpRenderer $renderer, ResponseFactoryInterface $responseFactory)
    {
        $this->renderer = $renderer;
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $layoutResponse = $this->responseFactory->createResponse();
        $this->renderer->render($layoutResponse, 'layout/layout.phtml', ['content' => $response->getBody()]);
        return $response->withBody($layoutResponse->getBody());
    }
}