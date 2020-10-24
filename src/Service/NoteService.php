<?php

namespace App\Service;

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
}