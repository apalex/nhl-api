<?php

namespace App\Services;

use App\Core\Result;
use App\Models\UserModel;
use App\Validation\Validator;

class RegisterService
{

    /**
     * RegisterService constructor
     *
     * @param UserModel $user_model The user model instance.
     */
    public function __construct(private UserModel $user_model) {}

    /**
     * Handles inserting a user into the database.
     *
     * @param array $user The incoming data to insert.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    function createUser(array $user) : Result
    {
        //* Validate Received Data
        //! Check if empty
        if (empty($user)) {
            return Result::failure("No user was provided for insertion.");
        }

        //* Validator
        $rules = array(
            "role" => [
                'required',
                ['in', ['user', 'admin']]
            ],
            "username" => [
                'required',
                'alphaNum',
                ['lengthMin', 3],
                ['lengthMax', 20],
                ['regex', '/^[a-zA-Z0-9_]+$/'],
                [function() use ($user) {
                    return !$this->user_model->checkUsernameInUse($user["username"]);
                }, 'is already in use!']
            ],
            "email" => [
                'required',
                'email',
                [function() use ($user) {
                    return !$this->user_model->checkEmailInUse($user["email"]);
                }, 'is already in use!']
            ],
            "password" => [
                'required',
                ['lengthMin', 6],
                ['lengthMax', 40]
            ]
        );

        $validator = new Validator($user, [], 'en');
        $validator->mapFieldsRules($rules);

        //* Result Pattern
        //* Unsuccessful
        if (!$validator->validate()) {
            return Result::failure("User failed validation.", $validator->errors());
        }

        //* Successful
        else {
            //* Insert new resource into model
            $this->user_model->insertUser($user);

            return Result::success("User was successfully created!", $user);
        }
    }
}
