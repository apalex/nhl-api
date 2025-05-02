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
     * Handles inserting a user into database.
     *
     * @param array $user The incoming data to insert.
     *
     * @return Result JSON response to encapsulate and return the result of Create Operation.
     *
     */
    function createUser(array $user) : Result
    {
        $errors = [];

        //* Validate Received Data
        //! Check if empty
        if (empty($user)) {
            return Result::failure("No user was provided for insertion.");
        }

        //* Validator
        foreach($user as $index => $u) {
            //* Validator Rules
            $rules = array(
                "role" => [
                    'required',
                    // ['regex', '']
                ],
                "username" => [
                    'required',
                    // ['regex', '']
                ],
                "email" => [
                    'required',
                    // ['regex', '']
                ],
                "password" => [
                    'required',
                    // ['regex', '']
                ]
            );

            //* Batch Validate
            $validator = new Validator($u, [], 'en');
            $validator->mapFieldsRules($rules);

            //* Invalid HTTP Response Message
            if (!$validator->validate()) {
                $errors[$index] = $validator->errors();
            }
        }

        //* Result Pattern
        //* Unsuccessful
        if (!empty($errors)) {
            return Result::failure("User failed validation.", $errors);
        }

        //* Successful
        else {
            //* Insert new resource into model
            $this->user_model->insertUser($user);

            return Result::success("User was successfully inserted!", $user);
        }
    }
}
