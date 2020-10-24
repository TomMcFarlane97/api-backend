<?php

namespace App\Repository;

use App\Entity\Note;
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

    use NoteDatabaseTrait;
}