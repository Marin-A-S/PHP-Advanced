<?php

namespace Geekbrains\Php2\Repositories;

use Geekbrains\Php2\Blog\Comment;
use Geekbrains\Php2\Blog\Exceptions\CommentNotFoundException;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\Name;
use Geekbrains\Php2\Person\User;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);  // объект обертка
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false); // настраиваем, что запрос fetch вернет false
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot find comment: d02eef61-1a06-460f-b859-202b84164734'); // ожидаемое соощение Exception
        $this->expectException(CommentNotFoundException::class); // ожидаем тип Exception
        $repository->get(new UUID('d02eef61-1a06-460f-b859-202b84164734')); // выполнение и результат
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':post_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':user_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':text' => 'Nikitin',
            ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteCommentsRepository($connectionStub);

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new Name('first_name', 'last_name'),
            'name',
        );

        $post = new Post (
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            $user,
            'Title',
            'Text'
        );

        $repository->save(
            new Comment(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                $post,
                'Nikitin'
            )
        );
    }

    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

         $statementMock->method('fetch')->willReturn([
            // Comment (необходимы все поля ответа)
            'uuid' => '0e1cbfc8-14e4-48a5-b602-20466ad5269c',
            'post_uuid' => '875b2353-cf39-49aa-a662-93dc36571a80',
            'user_uuid' => 'fcfab59b-2a88-44b8-a7bd-69fda8f2dcb8',
            'text' => 'Комментарий ко второй статье Светловой М.',
            'username' => 'User-4',
            'first_name' => 'Maria',
            'last_name' => 'Svetlova',
            'title' => 'Заголовок',
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqliteCommentsRepository($connectionStub);
        $post = $postRepository->get(new UUID('0e1cbfc8-14e4-48a5-b602-20466ad5269c'));
        $this->assertSame('0e1cbfc8-14e4-48a5-b602-20466ad5269c', (string)$post->uuid());
    }
}