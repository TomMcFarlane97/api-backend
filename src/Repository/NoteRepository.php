<?php

namespace App\Repository;

use App\Entity\Note;
use App\Exceptions\DatabaseException;
use App\Exceptions\RepositoryException;
use App\Traits\NoteDatabaseTrait;

/**
 * Class NoteRepository
 * @package App\Repository
 * @method null|Note find(int $id): ?Note
 * @method Note[] findAll(): Note[]
 * @method Note[] findBy(array $whereConditions = [], int $limit = null): Note[]
 * @method null|Note findOneBy(array $whereConditions = []): ?Note
 * @method Note insertSingle(string $columnNames, string $columnValues): Note
 * @method Note updateSingleByPrimaryKey(int $primaryKeyValue, string $updatedValues): Note
 * @method null deleteItem(int $primaryKeyValue): null
 */
class NoteRepository extends AbstractRepository
{
    protected string $tableName = 'notes';
    protected string $primaryKeyName = 'id';
    protected string $entityName = Note::class;
    /** @var string[] */
    protected array $foreignKeys = ['user_id'];

    use NoteDatabaseTrait;

    /**
     * @param Note $note
     * @return Note
     * @throws DatabaseException|RepositoryException
     */
    public function createNote(Note $note): Note
    {
        return $this->insertSingle(
            $this->getColumnKeysAsString(true),
            $this->getColumnValues($note)
        );
    }

    /**
     * @param Note $note
     * @return Note
     * @throws DatabaseException|RepositoryException
     */
    public function updateNote(Note $note): Note
    {
        return $this->updateSingleByPrimaryKey(
            $note->getId(),
            $this->getColumnValues($note, true)
        );
    }
}