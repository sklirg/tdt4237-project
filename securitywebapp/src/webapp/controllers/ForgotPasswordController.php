<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 30.08.2015
 * Time: 00:07
 */

namespace tdt4237\webapp\controllers;


class ForgotPasswordController extends Controller {

    public function __construct() {
        parent::__construct();
    }


    function forgotPassword() {
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        $this->render('forgotPassword.twig', ['csrf_token' => $_SESSION['csrf_token'],]);
    }

    function submitName() {
        $username = $this->app->request->post('username');
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/forgot');
        }
        else if($username != "") {
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
            $this->app->redirect('/forgot/' . $username . "?csrf_token=" . $_SESSION['csrf_token']);
        }
        else {
            $this->render('forgotPassword.twig');
            $this->app->flash("error", "Please input a username");
        }

    }

    function confirmForm($username) {
        if ($_GET['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/forgot');
        }
        else if($username != "") {
            $user = $this->userRepository->findByUser($username);
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
            $this->render('forgotPasswordConfirm.twig', ['user' => $user, 'csrf_token' => $_SESSION['csrf_token']]);
        }
        else {
            $this->app->flashNow("error", "Please write in a username");
        }
    }

    function confirm() {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/forgot');
        }
        $this->app->flash('success', 'Thank you! The password was sent to your email');
        // $sendmail

        $this->app->redirect('/login');
    }

    function deny() {

    }





}
/**
<?php

namespace tdt4237\webapp\controllers;


class ForgotPasswordController extends Controller {

    public function __construct() {
        parent::__construct();
    }


    function forgotPassword() {
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        $this->render('forgotPassword.twig', ['csrf_token' => $_SESSION['csrf_token'],]);
    }

    function submitName() {
        $username = $this->app->request->post('username');

        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/forgot');
        }
        else if($username != "") {
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
            $this->app->redirect('/forgot/' . $username, ['csrf_token' => $_SESSION['csrf_token']]);
            // $this->render('forgotPasswordConfirm.twig', ['username' => $username, 'csrf_token' => $_SESSION['csrf_token']]);
        }
        else {
            $this->render('forgotPassword.twig');
            $this->app->flash("error", "Please input a username");
        }

    }

    function confirmForm($username) {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/forgot/' . $username);
        }
        else if($username != "") {
            $user = $this->userRepository->findByUser($username);
            $this->render('forgotPasswordConfirm.twig', ['user' => $user]);
        }
        else {
            $this->app->flashNow("error", "Please write in a username");
        }
    }

    function confirm() {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
            $this->app->redirect('/forgot/');
        }
        $this->app->flash('success', 'Thank you! The password was sent to your email');
        // $sendmail

        $this->app->redirect('/login');
    }

    function deny() {

    }





}
*/