<?php

declare(strict_types=1);

namespace APIv1;

use JsonException;
use Notebook\NotebookFactory;
use Notebook\NotebookRepositoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slim\Interfaces\RouteParserInterface;

class NotebookController implements NotebookControllerInterface
{
    protected RouteParserInterface $router;

    public function __construct(
        readonly protected NotebookRepositoryInterface $notebookRepository,
        readonly protected string $apiUrl,
        readonly protected ContainerInterface $container
    ) {
        try {
            $router = $this->container->get('router');

            if (!$router instanceof RouteParserInterface) {
                throw new RuntimeException('Тип роутера не поддерживается');
            }

            $this->router = $router;
        } catch (ContainerExceptionInterface $e) {
            throw new RuntimeException(
                message : 'Ошибка получения роутера из контейнера',
                previous: $e
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function get(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $id = (int)$args['id'];

        $notebook         = $this->notebookRepository->get($id);
        $notebookResource = new NotebookResource($notebook);
        $response->getBody()->write($notebookResource->toJson());

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function list(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();
        $page        = (int)($queryParams['page'] ?? 1);
        $perPage     = (int)($queryParams['per_page'] ?? $this->notebookRepository::ITEMS_PER_PAGE);

        $notebooks = $this->notebookRepository->page($page, $perPage);
        $total     = $this->notebookRepository->count();
        $lastPage  = (int)(ceil($total / $perPage));

        $notebookListResource = new NotebookListResource(
            notebooks  : $notebooks,
            currentPage: $page,
            lastPage   : $lastPage,
            total      : $total
        );

        $response->getBody()->write($notebookListResource->toJson());

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function create(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $notebook = NotebookFactory::fromArray($this->getJsonBody($request));
        $this->notebookRepository->save($notebook);
        $notebookResource = new NotebookResource($notebook);
        $response->getBody()->write($notebookResource->toJson());
        $locationOfNewNotebook = $this->apiUrl . $this->router->urlFor(
            NamedRoutes::notebooks_get->value,
            ['id' => (string)($notebook->id ?? '')]
        );

        return $response
            ->withStatus(201)
            ->withHeader('Location', $locationOfNewNotebook);
    }

    /**
     * @inheritDoc
     */
    public function update(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $newNotebook = NotebookFactory::fromArray($this->getJsonBody($request));
        $oldNotebook = $this->notebookRepository->get((int)$args['id']);
        $oldNotebook->fillFrom($newNotebook);
        $this->notebookRepository->save($oldNotebook);
        $notebookResource = new NotebookResource($oldNotebook);
        $response->getBody()->write($notebookResource->toJson());

        return $response->withStatus(200);
    }

    /**
     * @return array<string, string|null>
     * @throws JsonException
     */
    protected function getJsonBody(ServerRequestInterface $request): array
    {
        $result = json_decode($request->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($result)) {
            throw new RuntimeException();
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $args
    ): ResponseInterface {
        $id       = (int)$args['id'];
        $notebook = $this->notebookRepository->get($id);
        $this->notebookRepository->delete($notebook);

        return $response->withStatus(204);
    }
}
