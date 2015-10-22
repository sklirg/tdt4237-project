<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Age;
use tdt4237\webapp\models\Email;
use tdt4237\webapp\models\User;
use tdt4237\webapp\validation\EditUserFormValidation;
use tdt4237\webapp\validation\RegistrationFormValidation;

class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
            return $this->render('newUserForm.twig', ['csrf_token' => $_SESSION['csrf_token']]);
        }

        $username = $this->auth->user()->getUserName();
        $this->app->flash('info', 'You are already logged in as ' . $username);
        $this->app->redirect('/');
    }

    public function create()
    {
        $request  = $this->app->request;
        $username = $request->post('user');
        $password = $request->post('pass');
        $retypePass = $request->post('retypepass');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');

        

        if ($this->userRepository->findByUser($username)) {
            $username = '-1';
        }

        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/user/new');
            return;
        }


        $validation = new RegistrationFormValidation($username, $password, $retypePass, $fullname, $address, $postcode);

        if ($validation->isGoodToGo()) {
            $password = $password;
            $password = $this->hash->createAPIHash($password);
            $user = new User($username, $password, $fullname, $address, $postcode);
            $this->userRepository->save($user);

            $this->app->flash('info', 'Thanks for creating a user. Now log in.');
            return $this->app->redirect('/login');
        }

        $errors = join("<br>\n", $validation->getValidationErrors());
        $this->app->flashNow('error', $errors);
        $this->render('newUserForm.twig', ['username' => $username,]);
    }

    public function all()
    {
        $this->render('users.twig', [
            'users' => $this->userRepository->all()
        ]);
    }

    public function logout()
    {
        $this->auth->logout();
        /*
        $this->app->deleteCookie('isadmin');
        $this->app->deleteCookie('PHPSESSID');
        */
        $this->app->redirect('/');
    }

    public function show($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash("info", "You must be logged in to do that");
            $this->app->redirect("/login");

        } else {
            $user = $this->userRepository->findByUser($username);

            if ($user != false && $user->getUsername() == $this->auth->getUsername()) {

                $this->render('showuser.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            } else if ($this->auth->check()) {

                $this->render('showuserlite.twig', [
                    'user' => $user,
                    'username' => $username
                ]);
            }
        }
    }

    public function showUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));

        $this->render('edituser.twig', [
            'user' => $this->auth->user(),
            'csrf_token' => $_SESSION['csrf_token'],
        ]);
    }

    public function receiveUserEditForm()
    {
        $this->makeSureUserIsAuthenticated();
        $user = $this->auth->user();

        $request = $this->app->request;
        $email   = $request->post('email');
        $bio     = $request->post('bio');
        $age     = $request->post('age');
        $fullname = $request->post('fullname');
        $address = $request->post('address');
        $postcode = $request->post('postcode');
        $ispayinguser = $request->post('ispayinguser');
        $bnr = $request->post('bnr');


        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/user/edit');
            return;
        }

        $validation = new EditUserFormValidation($email, $bio, $age);

        if ($validation->isGoodToGo()) {
            $user->setEmail(new Email($email));
            $user->setBio($bio);
            $user->setAge(new Age($age));
            $user->setFullname($fullname);
            $user->setAddress($address);
            $user->setPostcode($postcode);
            $user->setIspayinguser($ispayinguser);
            $user->setBnr($bnr);
            $this->userRepository->save($user);


            $this->app->flashNow('info', 'Your profile was successfully saved.');
            $this->userRepository->saveIsPaying($user);
            
            return $this->render('edituser.twig', ['user' => $user]);

        }

        $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
        $this->render('edituser.twig', ['user' => $user]);

    }

    public function makeSureUserIsAuthenticated()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
        }
    }
}
