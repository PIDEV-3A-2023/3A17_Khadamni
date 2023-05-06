<?php

namespace App\Security\Hasher;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CustomPasswordHasher implements PasswordHasherInterface
{
    use CheckPasswordLengthTrait;


    public function hash(string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }

        // ... hash the plain password in a secure way

        return hash('sha256',$plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }

        return hash('sha256',$plainPassword) === $hashedPassword;
    }

    public function needsRehash(string $hashedPassword): bool
    {
        // Check if a password hash would benefit from rehashing
        return false;
    }

}



