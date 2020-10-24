<?php

namespace App\Repository;

use App\Entity\Note;

/**
 * Class NoteRepository
 * @package App\Repository
 * @method null|Note find(int $id): ?Note
 * @method Note[] findAll(): Note[]
 * @method Note insertSingle(string $columnNames, string $columnValues): Note
 * @method Note updateSingleByPrimaryKey(int $primaryKeyValue, string $updatedValues): Note
 * @method null deleteItem(int $primaryKeyValue): null
 */
class NoteRepository extends AbstractRepository
{
    protected string $tableName = 'notes';
    protected string $primaryKeyName = 'id';
    protected string $entityName = Note::class;
}