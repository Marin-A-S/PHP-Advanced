<?php

namespace Geekbrains\Php2\Repositories;

use Geekbrains\Php2\Blog\Exceptions\PostNotFoundException;
use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\Name;
use Geekbrains\Php2\Person\User;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);  // объект обертка
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false); // настраиваем, что запрос fetch вернет false
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot find post: d02eef61-1a06-460f-b859-202b84164734'); // ожидаемое соощение Exception
        $this->expectException(PostNotFoundException::class); // ожидаем тип Exception
        $repository->get(new UUID('d02eef61-1a06-460f-b859-202b84164734')); // выполнение и результат
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '875b2353-cf39-49aa-a662-93dc36571a80',
                ':user_uuid' => '6d61d2d0-6bf8-426f-855a-dc9de949594b',
                ':title' => 'Заголовок',
                ':text' => 'Текст статьи',
            ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub);

        $user = new User(
            new UUID('6d61d2d0-6bf8-426f-855a-dc9de949594b'),
            new Name('Maria', 'Svetlova'),
            'User-4',
        );

        $repository->save(
            new Post(
                new UUID('875b2353-cf39-49aa-a662-93dc36571a80'),
                $user,
                'Заголовок',
                'Текст статьи'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            // Post
            'uuid' => '875b2353-cf39-49aa-a662-93dc36571a80',
            'user_uuid' => '6d61d2d0-6bf8-426f-855a-dc9de949594b',
            'title' => 'Заголовок',
            'text' => 'Текст статьи',
            // User
            'username' => 'User-4',
            'first_name' => 'Maria',
            'last_name' => 'Svetlova',
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub);
        $post = $postRepository->get(new UUID('875b2353-cf39-49aa-a662-93dc36571a80'));
        $this->assertSame('875b2353-cf39-49aa-a662-93dc36571a80', (string)$post->uuid());
    }
}
