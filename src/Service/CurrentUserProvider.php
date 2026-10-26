<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Stand-in service to provide the current authenticated user context until the security lesson.
 */
class CurrentUserProvider
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * Look up the stand-in admin user.
     * // TODO: replace with $this->getUser() after the security lesson
     */
    public function getAdmin(): ?User
    {
        return $this->userRepository->findOneBy(['email' => 'admin@antigo.com']);
    }

    /**
     * Look up the stand-in demo client user.
     * // TODO: replace with $this->getUser() after the security lesson
     */
    public function getClient(): ?User
    {
        return $this->userRepository->findOneBy(['email' => 'demo@client.com']);
    }
}
