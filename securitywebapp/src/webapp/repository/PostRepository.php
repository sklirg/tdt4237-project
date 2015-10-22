<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Post;
use tdt4237\webapp\models\PostCollection;

class PostRepository
{

    /**
     * @var PDO
     */
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public static function create($id, $author, $title, $content, $date)
    {
        $post = new Post;
        
        return $post
            ->setPostId($id)
            ->setAuthor($author)
            ->setTitle($title)
            ->setContent($content)
            ->setDate($date);
    }

    public function find($postId)
    {
        $sql  = "SELECT * FROM posts WHERE postId = $postId";
        $result = $this->db->query($sql);
        $row = $result->fetch();

        if($row === false) {
            return false;
        }


        return $this->makeFromRow($row);
    }

    public function all()
    {
        $sql   = "SELECT * FROM posts";
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
    }

        public function allDoctor()
    {

        //$sql   = "SELECT * FROM posts INNER JOIN users post ON posts.author=users.user INNER JOIN payingusers ON post.id=payingusers.id";
        $sql = "SELECT * FROM posts,users,payingusers WHERE posts.author = users.user AND payingusers.id = users.id AND payingusers.ispaying = 1";
        $results = $this->db->query($sql);

        if($results === false) {
            return [];
            throw new \Exception('PDO error in posts all()');
        }

        $fetch = $results->fetchAll();
        if(count($fetch) == 0) {
            return false;
        }

        return new PostCollection(
            array_map([$this, 'makeFromRow'], $fetch)
        );
    }

    public function makeFromRow($row)
    {
        return static::create(
            $row['postId'],
            $row['author'],
            $row['title'],
            $row['content'],
            $row['date']
        );

       //  $this->db = $db;
    }

    public function deleteByPostid($postId)
    {
        return $this->db->exec(
            sprintf("DELETE FROM posts WHERE postid='%s';", $postId));
    }


    public function save(Post $post)
    {
        $title              = $post->getTitle();
        $author             = $post->getAuthor();
        $content            = $post->getContent();
        $date               = $post->getDate();
        $isAnsweredByDoctor = $post->getDoctor();

        if ($post->getPostId() === null) {
            // Prepare SQL statement
            $stmt = $this->db->prepare("INSERT INTO posts (title, author, content, date, isAnsweredByDoctor) " .
            "VALUES (:title, :author, :content, :date, :isAnsweredByDoctor);"
            );
            // Bind parameters to their respective values
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":author", $author);
            $stmt->bindParam(":content", $content);
            $stmt->bindParam(":date", $date);
            $stmt->bindParam(":isAnsweredByDoctor", $isAnsweredByDoctor);
            // Execute query
            $stmt->execute();
        }

        // Seems like good practice....
        return $this->db->lastInsertId();
    }

    public function updateDoctor($postId, $status)
    {
        $stmt = $this->db->prepare("UPDATE posts SET isAnsweredByDoctor=:status WHERE postId=:postId;");
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":postId", $postId);
        $stmt->execute();
    }
}
