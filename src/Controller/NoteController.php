<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\EntityException;
use App\Exceptions\ImANumptyException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Helpers\ErrorResponse;
use App\Helpers\StatusCodes;
use App\Service\AuthenticationService;
use App\Service\NoteService;
use JsonException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class NoteController extends AbstractController
{
    private NoteService $noteService;

    /**
     * NoteController constructor.
     * @param AuthenticationService $authenticationService
     * @param LoggerInterface $logger
     * @param NoteService $noteService
     * @codeCoverageIgnore
     */
    public function __construct(
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        NoteService $noteService
    ) {
        parent::__construct($authenticationService, $logger);
        $this->noteService = $noteService;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException|ImANumptyException
     * @codeCoverageIgnore
     */
    public function getAll(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
            $notes = $this->noteService->getAllNotesForUser((int) $args['userId']);
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            [self::convertObjectToArray($notes)],
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws JsonException|EntityException
     * @codeCoverageIgnore
     */
    public function createNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
            $user = $this->noteService->createNote(
                (int) $args['userId'],
                json_decode(
                    $request->getBody()->getContents(),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
            StatusCodes::CREATED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException
     * @codeCoverageIgnore
     */
    public function getNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
            $note = $this->noteService->getNoteFromUser((int) $args['userId'], (int) $args['noteId']);
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $note->convertToArray(),
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @throws EntityException|JsonException
     * @codeCoverageIgnore
     */
    public function updateNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
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
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            $user->convertToArray(),
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string[] $args
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    public function deleteNote(RequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        try {
            $errorResponse = $this->validateRequest($request);
            if ($errorResponse instanceof ErrorResponse) {
                return $errorResponse;
            }
            $this->noteService->deleteNote((int) $args['userId'], (int) $args['noteId']);
        } catch (RequestException | DatabaseException | RepositoryException $exception) {
            return new JsonResponse(
                $this->getMessage($exception),
                $exception->getCode(),
                $this->jsonResponseHeader
            );
        }
        return new JsonResponse(
            [],
            StatusCodes::ACCEPTED,
            $this->jsonResponseHeader
        );
    }
}
