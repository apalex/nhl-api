<?php

namespace App\Services;

use App\Core\Result;
use App\Exceptions\HttpInvalidInputException;
use App\Helpers\LogHelper;
use App\Models\UserModel;
use App\Validation\Validator;

class LoginService
{

    /**
     * LoginService constructor
     *
     * @param UserModel $user_model The user model instance.
     */
    public function __construct(private UserModel $user_model) {}

    /**
     * Handles authenticating a user.
     *
     * @param array $user The incoming data to insert.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    function authenticate(array $user, $request) : array
    {
        //* Validate Received Data
        //! Check if empty
        if (empty($user)) {
            throw new HttpInvalidInputException($request, "No user was provided for authentication.");
        }

        //* Validate Data
        if (empty($user['username']) || empty($user['password'])) {
            throw new HttpInvalidInputException($request, "Please provide a valid username or password.");
        }

        // Retrieve user by username
        $db_user = $this->user_model->getUserByUsername($user['username']);

        if (!$db_user || !password_verify($user['password'], $db_user['password'])) {
            LogHelper::logError("Invalid login attempt", ['username' => $user['username']]);
            throw new HttpInvalidInputException($request, "Please provide a valid username or password.");
        }

        //* Successful
        return [
            'user_id' => $db_user['user_id'],
            'username' => $db_user['username'],
            'role' => $db_user['role']
        ];
    }
}
