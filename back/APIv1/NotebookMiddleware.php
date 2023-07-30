<?php

declare(strict_types=1);

namespace APIv1;

use DomainException;
use Exception;
use JsonException;
use Notebook\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\ValidationException;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class NotebookMiddleware
{
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandler $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request)->withHeader('Content-Type', 'application/json');
        } catch (ValidationException $e) {
            $response = new Response();
            $response->getBody()->write($e->getMessage());

            return $response->withStatus(400);
        } catch (DomainException | JsonException) {
            return (new Response())->withStatus(400);
        } catch (NotFoundException) {
            return (new Response())->withStatus(404);
        } catch (Exception) {
            return (new Response())->withStatus(500);
        }
    }
}
