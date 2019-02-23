<?php

namespace Hotel;

use Psr\Http\Message\ResponseInterface;

class HelloWorld
{
    /** @var string */
    private $foo;
    /** @var ResponseInterface */
    private $response;

    public function announce(): void
    {
        echo 'Hello, autoloaded world!';
    }

    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');
        $response->getBody()
            ->write("<html><head></head><body>Hello, {$this->foo} world!</body></html>");

        return $response;
    }

    public function __construct(
        string $foo,
        ResponseInterface $response
    )
    {
        $this->foo = $foo;
        $this->response = $response;
    }
}