<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class CommentEntity
{
    protected $comment_id;
    protected $quiz_id;
    protected $user_id;
    protected $comment;
    protected $created;
    # we need the username and picture columns from users table
    protected $username;
    protected $photo;

    public function getCommentId()
    {
        return $this->comment_id;
    }

    public function setCommentId($comment_id)
    {
        $this->comment_id = $comment_id;
        return $this;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;
        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
        return $this;
    }
}

