<?php

use App\Interfaces\Repository\AbstractRepositoryInterface;
use App\Interfaces\Repository\NoteRepositoryInterface;
use App\Interfaces\Repository\UserRepositoryInterface;
use App\Repository\AbstractRepository;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;

use function DI\autowire;

return [
    AbstractRepositoryInterface::class => autowire(AbstractRepository::class),
    UserRepositoryInterface::class => autowire(UserRepository::class),
    NoteRepositoryInterface::class => autowire(NoteRepository::class),
];
