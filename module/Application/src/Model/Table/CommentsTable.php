<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\CommentEntity; # <- add this use statement as well
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet; # add this use statement
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter;
use Laminas\Hydrator\ClassMethodsHydrator; # <- be sure to add this use statement
use Laminas\I18n;
use Laminas\InputFilter;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Validator;

class CommentsTable extends AbstractTableGateway
{
    protected $table = 'comments'; # I realize I mispelled comments here

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function deleteComment(int $commentId, int $userId)
    {
        $sqlQuery = $this->sql->delete()->where(['user_id' => $userId])->where(['comment_id' => $commentId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function fetchCommentById(int $commentId)
    {
        $sqlQuery = $this->sql->select()
            ->join('users', 'users.user_id='.$this->table.'.user_id', ['user_id', 'photo'])
            ->where(['comment_id' => $commentId]);
        
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result  = $sqlStmt->execute()->current();

        if (!$result) {
            return null;
        }

        $method = new ClassMethodsHydrator();
        $entity = new CommentEntity();
        $method->hydrate($result, $entity);

        return $entity;
    }

    public function fetchCommentsByQuizId(int $quizId, bool $paginate = false)
    {
        $sqlQuery = $this->sql->select()
            ->join('users', 'users.user_id='.$this->table.'.user_id', ['username', 'photo'])
            ->where(['quiz_id' => $quizId])
            ->order('created DESC');

        $entity = new CommentEntity();
        $method = new ClassMethodsHydrator();
        $resultSet = new HydratingResultSet($method, $entity);

        if ($paginate) {
            $paginatorAdapter = new DbSelect($sqlQuery, $this->adapter, $resultSet);
            return new Paginator($paginatorAdapter);
        }

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $results = $sqlStmt->execute();

        $resultSet->initialize($results);

        return $resultSet;
    }

    public function insertComment(array $data)
    {
        $values = [
            'comment' => $data['comment'],
            'user_id' => $data['user_id'],
            'quiz_id' => $data['quiz_id'],
            'created' => date('Y-m-d H:i:s')
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        
        return $sqlStmt->execute();
    }


    public function updateComment(array $data)
    {
        $values = [
            'comment' => $data['comment'],
            'user_id' => $data['user_id']
        ];


        $sqlQuery = $this->sql->update()->set($values)->where(['quiz_id' => (int) $data['quiz_id']]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function getCommentFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory     = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput([
                'name' => 'comment',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class]
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 2,
                            'max' => 500,
                            'messages' => [
                                Validator\StringLength::TOO_SHORT => 'Comment must have at least 2 characters',
                                Validator\StringLength::TOO_LONG => 'Text must have at most 500 characters'
                            ]
                        ]
                    ]
                ]
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'user_id',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\ToInt::class]
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => I18n\Validator\IsInt::class],
                    [
                        'name' => Validator\Db\RecordExists::class,
                        'options' => [
                            'table' => 'users',
                            'field' => 'user_id',
                            'adapter' => $this->adapter
                        ]
                    ]
                ]
            ])
        );

        $inputFilter->add(
            $factory->createInput([
                'name' => 'quiz_id',
                'required' => true,
                'filters' => [
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\ToInt::class]
                ],
                'validators' => [
                    ['name' => Validator\NotEmpty::class],
                    ['name' => I18n\Validator\IsInt::class],
                    [
                        'name' => Validator\Db\RecordExists::class,
                        'options' => [
                            'table' => 'quizzes',
                            'field' => 'quiz_id',
                            'adapter' => $this->adapter
                        ]
                    ]
                ]
            ])
        );
        
        return $inputFilter;
    }
}

