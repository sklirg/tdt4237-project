<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\Comment;
use tdt4237\webapp\validation\PostValidation;
use tdt4237\webapp\models\users;

class PostController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {

        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the posts page.");
            $this->app->redirect('/');
        }
       
        else {

             if ($this->auth->doctor()) {
                $posts = $this->postRepository->allDoctor();

             }

             else {
                $posts = $this->postRepository->all();
             }
                
        }

            $this->render('posts.twig', ['posts' => $posts]);
        

    }

    public function show($postId)
    {
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
        if ($this->auth->guest()) {
            $this->app->flash('info', "You must be logged in to view the posts page.");
            $this->app->redirect('/');
        }
        $post = $this->postRepository->find($postId);
        $comments = $this->commentRepository->findByPostId($postId);
        $request = $this->app->request;
        $message = $request->get('msg');
        $variables = [];

        if($message) {
            $variables['msg'] = $message;
        }

        $doctors = [];

        foreach ($comments as $comment)
        {
            $author = $comment->getAuthor();
            if ($this->userRepository->getIsDoctor($author) == 1)
            {
                $doctors[] = $author;
            }
        }

        $this->render('showpost.twig', [
            'post' => $post,
            'comments' => $comments,
            'csrf_token' => $_SESSION['csrf_token'],
            'flash' => $variables,
            'doctors' => $doctors
        ]);

    }

    public function addComment($postId)
    {
        if ($this->postRepository->checkAnsweredByDoctor($postId) == 0) {
            if($this->auth->doctor()) {
                //Add 7$ to doctor's wallet
                $user = $this->auth->user();
                $this->userRepository->saveEarnings($user, 7);
                //Add 7$ to the post-author spent.
                $authorName = $this->postRepository->find($postId)->getAuthor();
                $author = $this->userRepository->findByUser($authorName);
                //$author->setTotalpayed($author->getTotalPayed()+7);
                $this->userRepository->saveSpendings($author, 7);
                //Set doctoranswered flag.
                $post = $this->postRepository->find($postId);
                $post->setDoctor(1);
                $this->postRepository->saveExistingPost($post);
            }
        }
        if(!$this->auth->guest()) {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
                $this->app->redirect('/posts/' . $postId);
            }

            $isDoctor = $this->userRepository->getIsDoctor($_SESSION['user']);

            if ($isDoctor == 1) {
                $this->postRepository->updateDoctor($postId, 1);
            }

            $comment = new Comment();
            $comment->setAuthor($_SESSION['user']);
            $comment->setText($this->app->request->post("text"));
            $comment->setDate(date("dmY"));
            $comment->setPost($postId);
            $this->commentRepository->save($comment);
            $this->app->redirect('/posts/' . $postId);
        }
        else {
            $this->app->redirect('/login');
            $this->app->flash('info', 'you must log in to do that');
        }

    }

    public function showNewPostForm()
    {

        if ($this->auth->check()) {
            $username = $_SESSION['user'];
            // $data['csrf_token'] = md5(uniqid(rand(), true));
            $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
            $this->render('createpost.twig', ['username' => $username, 'csrf_token' => $_SESSION['csrf_token']]);
        } else {

            $this->app->flash('error', "You need to be logged in to create a post");
            $this->app->redirect("/");
        }

    }

    public function create()
    {
        if (!$this->auth->check()) {
            $this->app->flash("info", "You must be logged on to create a post");
            $this->app->redirect("/login");
        } else {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $this->app->flash("info", "Something went wrong. Please reload the page and try again.");
                $this->app->redirect("/posts/new");
            }
            $request = $this->app->request;
            $title = $request->post('title');
            $content = $request->post('content');
            $author = $_SESSION['user'];
            $date = date("dmY");

            $validation = new PostValidation($author, $title, $content);
            if ($validation->isGoodToGo()) {
                $currentUser = $this->auth->user();
                if ($this->userRepository->getIsPaying($author) == 1){
                    //Pay $3 for doctorvisibility
                    $this->userRepository->saveSpendings($currentUser, 3);
                }
                $post = new Post();
                $post->setAuthor($author);
                $post->setTitle($title);
                $post->setContent($content);
                $post->setDate($date);
                $post->setDoctor(0);
                $savedPost = $this->postRepository->save($post);
                $this->app->redirect('/posts/' . $savedPost . '?msg=Post successfully posted');
            } else {
                $this->app->flashNow('error', join('<br>', $validation->getValidationErrors()));
                $this->app->render('createpost.twig');
            }
        }
    }
}

