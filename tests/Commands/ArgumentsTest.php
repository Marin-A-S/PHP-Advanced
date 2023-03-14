<?php

namespace Geekbrains\Php2\Commands;

use Geekbrains\Php2\Blog\Commands\Arguments;
use Geekbrains\Php2\Blog\Exceptions\ArgumentsException;
use PHPUnit\Framework\TestCase;

class ArgumentsTest extends TestCase
{
    public function testItReturnsArgumentsValueByName(): void
    {
        // Подготовка
        $arguments = new Arguments(['some_key' => 123]);

        // Действие
        $value = $arguments->get('some_key');

        // Проверка
        // assertEquals - нестрогая assertSame - строгая
        $this->assertSame('123', $value);
        $this->assertIsString($value); // проверка строки
    }

    public function testItThrowsAnExceptionWhenArgumentIsAbsent(): void
    {
        // Подготавливаем объект с пустым набором данных
        $arguments = new Arguments([]);

        // Описываем тип ожидаемого исключения
        $this->expectException(ArgumentsException::class);

        // и его сообщение
        $this->expectExceptionMessage("No such argument: some_key");

        // Выполняем действие, приводящее к выбрасыванию исключения
        $arguments->get('some_key');
    }

    // Связываем тест с провайдером данных с помощью аннотации @dataProvider

    /**
     * @dataProvider argumentsProvider
     */
    public function testItConvertsArgumentsToStrings($inputValue, $expectedValue): void
    {
        // Подставляем первое значение из тестового набора
        $arguments = new Arguments(['some_key' => $inputValue]);
        $value = $arguments->get('some_key');

        // Сверяем со вторым значением из тестового набора
        $this->assertEquals($expectedValue, $value);
//        $this->assertSame($expectedValue, $value);
    }

    // Провайдер данных
    public function argumentsProvider(): iterable
    {
        return [
            ['some_string', 'some_string'], // Тестовый набор
            // Первое значение будет передано в тест первым аргументом,
            // второе значение будет передано в тест вторым аргументом
            [' some_string', 'some_string'], // Тестовый набор №2
            [' some_string ', 'some_string'],
            [123, '123'],
            [12.3, '12.3'],
        ];
    }

    public function testItReturnsArg(): void
    {
        // Подготовка
        $arguments = new Arguments([]); // пустого объекта
        $argumentsExpected = new Arguments(['username' => 'Admin']); // ожидаемого объект

        // Действие, отправка данных из командной строки
        $value = $arguments->fromArgv(['1' => 'username=Admin']); //

        // Проверка
        $this->assertIsObject($value); // проверка, что возвращается объект
        $this->assertEquals($argumentsExpected, $value); // сравнение двух объектов
    }

    public function testItOperatorContinueConstruct(): void
    {
        // Подготовка
        $arguments = new Arguments([]);

        // Выполняем действие, создаем объект с входным набором данных
        $argumentsReceived = new Arguments(['username' => '']);

        // Проверка
        $this->assertEquals($arguments, $argumentsReceived);

    }

    public function testItOperatorContinueArg(): void
    {

        // Подготовка
        $arguments = new Arguments([]);

        // Действие
        $value = $arguments->fromArgv(['1' => 'usernameAdmin']); //

        // Проверка
        $this->assertEquals($arguments, $value);
    }
}
