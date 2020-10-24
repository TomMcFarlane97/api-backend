<?php

namespace App\Service;

use App\Controller\AbstractController;
use App\Entity\Note;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Exceptions\RequestException;
use App\Repository\NoteRepository;

class NoteService
{
    private NoteRepository $notesRepository;
    private UserService $userService;

    public function __construct(NoteRepository $notesRepository, UserService $userService)
    {
        $this->notesRepository = $notesRepository;
        $this->userService = $userService;
    }

    /**
     * @param int $userId
     * @return Note[]
     * @throws DatabaseException|RepositoryException|RequestException
     */
    public function getAllNotesForUser(int $userId): array
    {
        return $this->notesRepository->findBy([
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
        $note = $this->notesRepository->findOneBy([
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
}