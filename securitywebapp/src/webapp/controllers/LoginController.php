<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\repository\UserRepository;

class LoginController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->check()) {
            $username = $this->auth->user()->getUsername();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
            return;
        }

        $this->render('login.twig', []);
    }

    public function login()
    {
        $request = $this->app->request;
        $user    = $request->post('user');
        $pass    = $request->post('pass');

        if ($this->auth->checkCredentials($user, $pass)) {
            if(!isset($_SESSION)){
                session_start();
            }
            else {
                session_regenerate_id(true);
            }
            $_SESSION['user'] = $user;
            //setcookie("user", $user, time() + 3600);
            //setcookie("password",  $pass, time() + 3600);
            $isAdmin = $this->auth->user()->isAdmin();

            if ($isAdmin) {
                setcookie("isadmin", "yes", time() + 3600);
            } else {
                setcookie("isadmin", "no", time() + 3600);
            }

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        }
        
        $this->app->flashNow('error', 'Incorrect user/pass combination.');
        $this->render('login.twig', []);
    }
}
