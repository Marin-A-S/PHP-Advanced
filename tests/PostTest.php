<?php

namespace Geekbrains\Php2;

use Geekbrains\Php2\Blog\Post;
use Geekbrains\Php2\Blog\UUID;
use Geekbrains\Php2\Person\Name;
use Geekbrains\Php2\Person\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testItSavingTitlePost(): void
    {
        $post = $this->postExample();

        $post->setTitle('Заголовок изменен');

        $this->assertSame('Заголовок изменен', $post->getTitle());
    }

    public function testItSavingTextPost(): void
    {
        $post = $this->postExample();

        $post->setText('Новый текст');

        $this->assertSame('Новый текст', $post->getText());
    }

    public function testItChangeAuthorPost(): void
    {
        $post = $this->postExample();

        $user = $post->getAuthor();
        $user->setName(new Name('Иван', 'Петров'));
        $post->setAuthor($user);

        $strAuthor = $post->getAuthor()->getName()->getFirstName() . ' ' . $post->getAuthor()->getName()->getLastName();
        $this->assertSame('Иван Петров', $strAuthor);
    }

    private function postExample(): Post
    {
        return
            new Post(
                new UUID('875b2353-cf39-49aa-a662-93dc36571a80'),
                new User (
                    new UUID('875b2353-cf39-49aa-a662-93dc36571a80'),
                    new Name('Maria', 'Svetlova'),
                    'User-4'
                ),
                'Заголовок',
                'Текст статьи'
            );
    }
}