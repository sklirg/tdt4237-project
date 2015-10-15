<?php

namespace tdt4237\webapp\repository;

use PDO;
use tdt4237\webapp\models\Comment;

class CommentRepository
{

    /**
     * @var PDO
     */
    private $db;

    const SELECT_BY_ID = "SELECT * FROM moviereviews WHERE id = %s"; // This one does nothing afaik... moviereviews sounds like an old version

    public function __construct(PDO $db)
    {

        $this->db = $db;
    }

    public function save(Comment $comment)
    {
        $id = $comment->getCommentId();
        $author  = $comment->getAuthor();
        $text    = $comment->getText();
        $date = (string) $comment->getDate();
        $postid = $comment->getPost();



        if ($comment->getCommentId() === null) {
            // Prepare SQL statement
            $stmt = $this->db->prepare('INSERT INTO comments (author, text, date, belongs_to_post) '.
                "VALUES (:author, :text, :date, :postid)"
            );
            // Bind parameters to their respective values
            $stmt->bindParam(":author", $author);
            $stmt->bindParam(":text", $text);
            $stmt->bindParam(":date", $date);
            $stmt->bindParam(":postid", $postid);
            // Execute query
            return $stmt->execute();
        }
    }

    public function findByPostId($postId)
    {
        $query   = "SELECT * FROM comments WHERE belongs_to_post = $postId";
        $rows = $this->db->query($query)->fetchAll();

        return array_map([$this, 'makeFromRow'], $rows);
    }

    public function makeFromRow($row)
    {
        $comment = new Comment;
        
        return $comment
            ->setCommentId($row['commentId'])
            ->setAuthor($row['author'])
            ->setText($row['text'])
            ->setDate($row['date'])
            ->setPost($row['belongs_to_post']);
    }
}
