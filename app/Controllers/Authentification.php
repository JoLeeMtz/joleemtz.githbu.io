<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Hash;
use App\Models\UserModel;

class Authentification extends BaseController
{
/* Constructors ************************************************************ */
    /**
     * Default Constructor
     */
    public function __construct()
    {
        helper(['url', 'form']);
    }
/* ************************************************************************* */

/* Login ******************************************************************* */
    /**
     * Login View Screen - Views->login.php
     */
    public function getIndex()
    {
        return view('authentification/login');
    }
    /**
     * Login View Screen - Views->register.php
     */
    public function getLogin()
    {
        return view('authentification/login');
    }

    
    /**
     * Login user - Views->login.php
     */
    public function postLogin()
    {
        // Validation for inputs in user
        $valid = $this->validate([
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Username is required.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password is required.'
                ]
            ]
        ]);

        // If input(s) aren't valid
        if(!$valid)
        {
            return view('authentification/login', ['validation' => $this->validator]);
        }

        // Get POST informations from submit
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $dbUserInfo = $userModel->where('username', $username)->first();
        if(!isset($dbUserInfo))
        {
            session()->setFlashdata('fail', 'Wrong password provided');
            return redirect()->to('authentification');
        }
        $validatePassword = password_verify($password, $dbUserInfo['password']);

        // Right password for the username provided
        if(isset($validatePassword) && $validatePassword)
        {
            $userId = $dbUserInfo['id'];
            session()->set('loggedUserId', $userId);
            return redirect()->to('FileManager');
        }
        else
        {
            session()->setFlashdata('fail', 'Wrong password provided');
            return redirect()->to('authentification');
        }
    }
/* ************************************************************************* */

/* Register **************************************************************** */

    /**
     * Register View Screen - Views->register.php
     */
    public function getRegister()
    {
        return view('authentification/register');
    }

    /**
     * Save user - Views->register.php
     */
    public function postRegisterUser()
    {
        // Validation for inputs in register
        $valid = $this->validate([
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'A username is required.'
                ]
            ],
            'name' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'A name is required.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[9]|max_length[22]',
                'errors' => [
                    'required' => 'A password is required.',
                    'min_length' => 'Password must be at least 9 characters.',
                    'max_length' => 'Password maximum contain 22 characters.'
                ]
            ],
            'confirmPassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'This input can\'t be empty.',
                    'matches' => 'Must match password.'
                ]
            ]
        ]);

        // If input(s) aren't valid
        if(!$valid)
        {
            return view('authentification/register', ['validation' => $this->validator]);
        }

        // Get POST informations from submit
        $username = $this->request->getPost('username');
        $name = $this->request->getPost('name');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirmPassword');
        // Setup data for insert query
        $data = [
            'username' => $username,
            'name' => $name,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ];

        $userModel = new UserModel();
        $query = $userModel->insert($data);

        // User got created successfully
        if(isset($query))
        {
            return redirect()->back()->with('success', 'User created successfully.');
        }
        else
        {
            return redirect()->back()->with('fail', 'Error on creating user.');
        }
    }
/* ************************************************************************* */

/* Logout ****************************************************************** */
    /**
     * Logout View Screen - Views->logout.php
     */
    public function getLogout()
    {
        if (session()->has('loggedUserId'))
        {
            session()->remove('loggedUserId');
        }
        return redirect()->to('/authentification?access=loggedOut')->with('fail', 'You are not connected.');
    }
/* ************************************************************************* */
}
