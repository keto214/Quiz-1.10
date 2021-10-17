<?php

declare(strict_types=1);

namespace Application\Form\Comment;

use Laminas\Form\Element;
use Laminas\Form\Form;

class CreateForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('new_comment');
        $this->setAttribute('method', 'post');

        # add the comment textarea field
        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'comment',
            'options' => [
                'label' => 'Leave a comment'
            ],
            'attributes' => [
                'required' => true,
                'row' => 3,
                'maxlength' => 500,
                'title' => 'Provide your comment',
                'class' => 'form-control',
                'placeholder' => 'Type a comment...'
            ]
        ]);

        # add the user_id hidden field
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'user_id'
        ]);

        # add the quiz_id hidden field
        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'quiz_id'
        ]);

        # add the crsf field
        $this->add([
            'type' => Element\Csrf::class,
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 1400
                ]
            ]
        ]);

        # add the submit button
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'post_comment',
            'attributes' => [
                'value' => 'Post Comment',
                'class' => 'btn btn-primary btn-lg btn-block'
            ]
        ]);
    }
}

