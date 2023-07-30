<?php

declare(strict_types=1);

use APIv1\NamedRoutes;
use APIv1\NotebookControllerInterface;
use APIv1\NotebookMiddleware;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require_once __DIR__ . "/vendor/autoload.php";

/**
 * Configure env
 */
(getenv('MODE') === 'test')
    ? $dotenv = Dotenv::createImmutable(__DIR__ . '/tests')
    : $dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Configure container
 */
$container = new DI\Container();
$builder   = new DI\ContainerBuilder();
$builder->addDefinitions(require 'container.php');
$container = $builder->build();

/**
 * Configure Slim
 */
AppFactory::setContainer($container);
$app = AppFactory::create();
$container->set('router', $app->getRouteCollector()->getRouteParser());

/**
 * Router
 */
$app->group('/api/v1/notebooks', function ($app) {
    $app->get('/{id:[0-9]+}', [NotebookControllerInterface::class, 'get'])->setName(NamedRoutes::notebooks_get->value);
    $app->get('', [NotebookControllerInterface::class, 'list']);
    $app->post('', [NotebookControllerInterface::class, 'create']);
    $app->put('/{id:[0-9]+}', [NotebookControllerInterface::class, 'update']);
    $app->delete('/{id:[0-9]+}', [NotebookControllerInterface::class, 'delete']);
})->add(new NotebookMiddleware());

$app->run();
