<?php

namespace App\Service;

use App\Controller\AbstractController;
use App\Entity\Note;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Repository\NoteRepository;
use App\Traits\BuildEntityLoopTrait;

// phpcs:disable
/**
 * Class NoteService
 * @package App\Service
 * @method Note buildEntity(string $entityString, array $entityBody, array $columnSetters, bool $isUpdate = false, Note $currentEntity = null): Note
 */
// phpcs:enable
class NoteService
{
    use BuildEntityLoopTrait;

    private NoteRepository $repository;
    private UserService $userService;

    public function __construct(NoteRepository $notesRepository, UserService $userService)
    {
        $this->repository = $notesRepository;
        $this->userService = $userService;
    }

    /**
     * @param int $userId
     * @return Note[]
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function getAllNotesForUser(int $userId): array
    {
        return $this->repository->findBy([
            'user_id' => $this->userService->retrieveUser($userId)->getId(),
        ]);
    }

    /**
     * @param int $userId
     * @param int $noteId
     * @return Note
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function getNoteFromUser(int $userId, int $noteId): Note
    {
        $note = $this->repository->findOneBy([
            'id' => $noteId,
            'user_id' => $this->userService->retrieveUser($userId)->getId(),
        ]);

        if (!$note) {
            throw new RequestException(
                sprintf('Unable to find note "%s", for user "%s"', $noteId, $userId),
                AbstractController::BAD_REQUEST
            );
        }

        return $note;
    }

    /**
     * @param int $userId
     * @param array<string, mixed> $noteBody
     * @return Note
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function createNote(int $userId, array $noteBody): Note
    {
        $user = $this->userService->retrieveUser($userId);
        $note = $this->buildEntity(
            $this->repository->getEntityName(),
            $noteBody,
            $this->repository->getColumnSetters(true, true),
        );
        $note->setUserId($user->getId());
        return $this->repository->createNote($note);
    }

    /**
     * @param int $userId
     * @param int $noteId
     * @param array<string, mixed> $noteBody
     * @return Note
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function updateNote(int $userId, int $noteId, array $noteBody): Note
    {
        $user = $this->userService->retrieveUser($userId);
        $note = $this->buildEntity(
            $this->repository->getEntityName(),
            $noteBody,
            $this->repository->getColumnSetters(true, true),
            true,
            $this->getNoteFromUser($user->getId(), $noteId)
        );
        return $this->repository->updateNote($note);
    }

    /**
     * @param int $userId
     * @param int $noteId
     * @return void
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function deleteNote(int $userId, int $noteId): void
    {
        $this->repository->deleteNote(
            $this->getNoteFromUser($userId, $noteId)
        );
    }
}
