<?php

namespace App\Helpers;

/**
 * Trait for password hashing and verification using bcrypt.
 */
trait PasswordTrait
{
    /**
     * Hashes a password using bcrypt.
     *
     * @param string $password Plain text password.
     * @return string The hashed password.
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifies a password against a hashed value.
     *
     * @param string $password Plain text password.
     * @param string $hash     The hashed password from the database.
     * @return bool True if password matches the hash, false otherwise.
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
