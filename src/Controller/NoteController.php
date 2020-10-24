<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Service\NoteService;
use JsonException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class NoteController extends AbstractController
{
    private NoteService $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException|ImANumptyException
     */
    public function getAll(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $notes = $this->noteService->getAllNotesForUser((int) $args['userId']);
        } catch (RequestException|DatabaseException|RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            [self::convertObjectToArray($notes)],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws JsonException|EntityException
     */
    public function createNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $user = $this->noteService->createNote(
                (int) $args['userId'],
                json_decode(
                    $request->getBody()->getContents(),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (RequestException|DatabaseException|RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException
     */
    public function getNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $note = $this->noteService->getNoteFromUser((int) $args['userId'], (int) $args['noteId']);
        } catch (RequestException|DatabaseException|RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $note->convertToArray(),
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException|JsonException
     */
    public function updateNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $this->validateRequestIsJson($request);
            $user = $this->noteService->updateNote(
                (int) $args['userId'],
                (int) $args['noteId'],
                json_decode(
                    $request->getBody()->getContents(),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (RequestException|DatabaseException|RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     */
    public function deleteNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        try {
            $this->validateRequestIsJson($request);
            $this->noteService->deleteNote((int) $args['userId'], (int) $args['noteId']);
        } catch (RequestException|DatabaseException|RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            [],
            self::ACCEPTED,
            $this->jsonResponseHeader
        );
    }
}