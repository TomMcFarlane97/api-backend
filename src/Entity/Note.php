<?php

namespace App\Entity;

use App\Interfaces\ConvertToArrayInterface;
use App\Traits\ConvertToArrayTrait;
use App\Traits\NoteDatabaseTrait;

class Note implements ConvertToArrayInterface
{
    use NoteDatabaseTrait;
    use ConvertToArrayTrait;

    private int $id;
    private string $note;
    private int $user_id;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }
}
