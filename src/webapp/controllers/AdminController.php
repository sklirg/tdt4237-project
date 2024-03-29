<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;
use tdt4237\webapp\models\User;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        $variables = [
            'users' => $this->userRepository->all(),
            'posts' => $this->postRepository->all()
        ];
        $this->render('admin.twig', $variables);
    }

    public function delete($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }
        $a = $this->userRepository->deleteByUsername($username);
        if ($a == 1) {
            $this->app->flash('info', "Sucessfully deleted '$username'");
            $this->app->redirect('/admin');
            return;
        }
        
        $this->app->flash('info', "An error ocurred. Unable to delete user '$username'.");
        $this->app->redirect('/admin');
    }

    public function deletePost($postId)
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }
        
        if ($this->postRepository->deleteByPostid($postId) === 1) {
            $this->app->flash('info', "Sucessfully deleted '$postId'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to delete post '$postId'.");
        $this->app->redirect('/admin');
    }
    public function grantDoctor($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        if ($this->userRepository->grantStatus($username) == 1) {
            $this->app->flash('info', "Status granted to '$username'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to grant status to '$username'.");
        $this->app->redirect('/admin');
    }

    public function revokeDoctor($username)
    {
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the admin page.");
            $this->app->redirect('/');
        }

        if (! $this->auth->isAdmin()) {
            $this->app->flash('info', "You must be administrator to view the admin page.");
            $this->app->redirect('/');
        }

        if ($this->userRepository->revokeStatus($username) == 1) {
            $this->app->flash('info', "Status revoked from '$username'");
            $this->app->redirect('/admin');
            return;
        }

        $this->app->flash('info', "An error ocurred. Unable to revoke status from '$username'.");
        $this->app->redirect('/admin');
    }
}
