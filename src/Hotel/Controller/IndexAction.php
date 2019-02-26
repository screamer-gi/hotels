<?php

namespace Hotel\Controller;

use Common\DbInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

class IndexAction
{
    /** @var ResponseFactoryInterface */
    private $responseFactory;

    /** @var PhpRenderer */
    private $renderer;

    /** @var DbInterface */
    private $db;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        PhpRenderer $renderer,
        DbInterface $db
    ) {
        $this->responseFactory = $responseFactory;
        $this->renderer = $renderer;
        $this->db = $db;
    }

    public function __invoke(): ResponseInterface
    {
        return $this->renderer->render($this->responseFactory->createResponse(), 'hotels/index.phtml', [
            'list' => $this->db->intervals()->orderBy('date_start')->fetchAll(),
        ]);
    }
}