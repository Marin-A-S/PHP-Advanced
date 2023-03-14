<?php

namespace Geekbrains\Php2\Repositories;

use Geekbrains\Php2\Blog\Exceptions\UserNotFoundException;
use Geekbrains\Php2\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\Name;
use Geekbrains\Php2\Person\User;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class SqliteUsersRepositoryTest extends TestCase
{
    // Тест, проверяющий, что SQLite-репозиторий бросает исключение,
    // когда запрашиваемый пользователь не найден
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionMock);
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');

        $repository->getByUsername('Ivan');
    }

    // Тест, проверяющий, что репозиторий сохраняет данные в БД
    public function testItSavesUserToDatabase(): void
    {
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);
        // 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);
        // 5. Описываем ожидаемое взаимодействие нашего репозитория с моком запроса
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод execute
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '6d61d2d0-6bf8-426f-855a-dc9de949594b',
                ':username' => 'User-4',
                ':first_name' => 'Maria',
                ':last_name' => 'Svetlova',
            ]);
        // 3. При вызове метода prepare стаб подключения возвращает мок запроса
        $connectionStub->method('prepare')->willReturn($statementMock);

        // 1. Передаём в репозиторий стаб подключения
        $repository = new SqliteUsersRepository($connectionStub);

        // Вызываем метод сохранения пользователя
        $repository->save(
            new User( // Свойства пользователя точно такие,
            // как и в описании мока
                new UUID('6d61d2d0-6bf8-426f-855a-dc9de949594b'),
                new Name('Maria', 'Svetlova'),
                'User-4',
            )
        );
    }

    public function testItGetUserByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            // User
            'uuid' => '6d61d2d0-6bf8-426f-855a-dc9de949594b',
            'username' => 'User-4',
            'first_name' => 'Maria',
            'last_name' => 'Svetlova',
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqliteUsersRepository($connectionStub);
        $post = $postRepository->get(new UUID('6d61d2d0-6bf8-426f-855a-dc9de949594b'));
        $this->assertSame('6d61d2d0-6bf8-426f-855a-dc9de949594b', (string)$post->uuid());
    }
}
