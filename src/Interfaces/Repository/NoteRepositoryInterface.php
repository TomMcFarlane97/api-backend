<?php

namespace App\Interfaces\Repository;

use App\Entity\Note;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;

interface NoteRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * @param Note $note
     * @return Note
     * @throws DatabaseException|RepositoryException
     */
    public function createNote(Note $note): Note;

    /**
     * @param Note $note
     * @return Note
     * @throws DatabaseException|RepositoryException
     */
    public function updateNote(Note $note): Note;

    /**
     * @param Note $note
     * @return void
     * @throws DatabaseException|RepositoryException
     */
    public function deleteNote(Note $note): void;
}
