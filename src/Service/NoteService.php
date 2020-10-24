<?php

namespace App\Service;

use App\Repository\NoteRepository;
use App\Repository\UserRepository;

class NoteService
{
    private NoteRepository $notesRepository;
    private UserRepository $userRepository;

    public function __construct(NoteRepository $notesRepository, UserRepository $userRepository)
    {
        $this->notesRepository = $notesRepository;
        $this->userRepository = $userRepository;
    }
}