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

        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        $this->render('login.twig', ['csrf_token' => $_SESSION['csrf_token'],]);
    }

    public function login()
    {
        $request = $this->app->request;
        $user    = $request->post('user');
        $pass    = $request->post('pass');

        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/login');
        }

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

            /*
            $isAdmin = $this->auth->user()->isAdmin();

            if ($isAdmin) {
                $this->app->setEncryptedCookie("isadmin", "yes");
            } else {
                $this->app->setEncryptedCookie("isadmin", "no");
            }
            */

            $this->app->flash('info', "You are now successfully logged in as $user.");
            $this->app->redirect('/');
            return;
        }
        
        $this->app->flashNow('error', 'Incorrect user/pass combination.');
        $this->render('login.twig', []);
    }
}
