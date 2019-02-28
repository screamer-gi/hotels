<?php

namespace Common;

use DI\ContainerBuilder;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Hotel\Api\AddAction as ApiAddAction;
use Hotel\Api\DeleteAction as ApiDeleteAction;
use Hotel\Api\EditAction as ApiEditAction;
use Hotel\Controller\AddAction;
use Hotel\Controller\DeleteAction;
use Hotel\Controller\EditAction;
use Hotel\Controller\IndexAction;
use Middlewares\FastRoute;
use Middlewares\JsonPayload;
use Middlewares\RequestHandler;
use Middlewares\Whoops;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;
use Slim\Views\PhpRenderer;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use function DI\autowire;
use function DI\create;
use function DI\factory;
use function FastRoute\simpleDispatcher;

class Application
{
    /** @var ContainerInterface */
    public $container;

    /** @var Dispatcher */
    private $routes;

    /** @var RequestHandlerInterface */
    private $requestHandler;

    /** @var EmitterInterface */
    private $emitter;

    public function configure(): self
    {
        $this->container = $this->configureContainer();
        $this->routes = $this->configureRouting();
        $middleware = $this->configureMiddleware();
        $this->requestHandler = new Relay($middleware);
        $this->emitter = new SapiEmitter();

        return $this;
    }

    public function run(): bool
    {
        $response = $this->requestHandler->handle(ServerRequestFactory::fromGlobals());

        return $this->emitter->emit($response);
    }

    private function configureContainer(): ContainerInterface
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->useAutowiring(true);
        $containerBuilder->useAnnotations(false);
        $containerBuilder->addDefinitions([
            DbInterface::class => factory(DbFactory::class),
            PhpRenderer::class => create()->constructor(__DIR__ . '/../../templates'),
            ResponseFactoryInterface::class => create(ResponseFactory::class),
            LayoutMiddleware::class => autowire(),
        ]);

        /** @noinspection PhpUnhandledExceptionInspection */
        return $containerBuilder->build();
    }

    private function configureRouting(): Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $r) {
            $r->get('/', IndexAction::class);
            $r->addRoute(['GET', 'POST'], '/add', AddAction::class);
            $r->addRoute(['GET', 'POST'], '/edit/{id}', EditAction::class);
            $r->post('/delete', DeleteAction::class);
            $r->addGroup('/api/intervals', function (RouteCollector $r) {
                $r->post('', ApiAddAction::class);
                $r->put('/{id}', ApiEditAction::class);
                $r->delete('/{id}', ApiDeleteAction::class);
            });
        });
    }

    /**
     * @return MiddlewareInterface[]
     */
    private function configureMiddleware(): array
    {
        $middlewareQueue[] = new Whoops();
        $middlewareQueue[] = (new JsonPayload())->depth(64);
        /** @noinspection PhpUnhandledExceptionInspection */
        $middlewareQueue[] = $this->container->get(LayoutMiddleware::class);
        $middlewareQueue[] = new FastRoute($this->routes);
        $middlewareQueue[] = new RequestHandler($this->container);

        return $middlewareQueue;
    }
}