<?php

use Common\DbFactory;
use Common\DbInterface;
use Common\LayoutMiddleware;
use Common\ResponseFactory;
use DI\ContainerBuilder;
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
use Psr\Http\Message\ResponseFactoryInterface;
use Relay\Relay;
use Slim\Views\PhpRenderer;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use function DI\autowire;
use function DI\create;
use function DI\factory;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    DbInterface::class => factory(DbFactory::class),
    PhpRenderer::class => create()->constructor(__DIR__ . '/../templates'),
    ResponseFactoryInterface::class => create(ResponseFactory::class),
    LayoutMiddleware::class => autowire(),
]);

/** @noinspection PhpUnhandledExceptionInspection */
$container = $containerBuilder->build();

$routes = simpleDispatcher(function (RouteCollector $r) {
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

$middlewareQueue[] = new Whoops();
$middlewareQueue[] = (new JsonPayload())->depth(64);
/** @noinspection PhpUnhandledExceptionInspection */
$middlewareQueue[] = $container->get(LayoutMiddleware::class);
$middlewareQueue[] = new FastRoute($routes);
$middlewareQueue[] = new RequestHandler($container);

$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
return $emitter->emit($response);
