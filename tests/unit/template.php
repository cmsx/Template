<?php

require_once __DIR__ . '/../init.php';

use CMSx\Template;
use CMSx\Template\Exception;

class TemplateTest extends PHPUnit_Framework_TestCase
{
  protected $path;

  function testNoPath()
  {
    Template::SetPath(null); //Удаляем предзаданный в setUp() путь

    $path = realpath(Template::GetPath());
    $exp  = realpath(__DIR__ . '/../../src/CMSx');
    $this->assertEquals($exp, $path, 'По умолчанию - путь к файлу с классом');
  }

  function testPath()
  {
    $this->assertEquals($this->path, Template::GetPath(), 'Путь к папке с шаблонами, заданный в setUp()');
    $this->assertEquals($this->path.'/hello.php', Template::GetPathToTemplate('hello.php'), 'Путь к шаблону #1');
    $this->assertEquals($this->path.'/hello.php', Template::GetPathToTemplate('/hello.php'), 'Путь к шаблону #2');
  }

  function testTemplateExist()
  {
    $this->assertTrue(Template::CheckTemplateExists('hello.php'), 'Шаблон существует');
    $this->assertFalse(Template::CheckTemplateExists('one.php'), 'Шаблон не существует');
  }

  function testRender()
  {
    $exp = 'Hello, World! Yeah, World!';
    $var = array('hello' => 'World');

    $t = new Template;
    $t->setTemplate('hello.php');
    $t->fromArray($var);
    $this->assertEquals($exp, $t->render(), 'Настройки через сеттеры');

    $t = new Template('hello.php', $var);
    $t->set('template', 123); //Подстава с заменой переменных при extract`е
    $this->assertEquals($exp, $t->render(), 'Настройки в конструкторе');

    $this->assertEquals($t->render(), (string)$t, 'Приведение к строке');

    $t = new Template;
    $t->fromArray($var);
    $this->assertEquals($exp, $t->render('hello.php'), 'Выбор шаблона при рендеринге');
  }

  function testException()
  {
    $t = new Template('exception.php');
    $this->assertEmpty($t->render(), 'Без Debug-режима информация скрыта');
    $this->assertEmpty((string)$t, 'Приведение к строке без отладки');

    Template::EnableDebug();

    $exp = 'CMSx\Template\Exception: [123] Демонстрация появления ошибок в шаблоне';
    $this->assertEquals($exp, $t->render(), 'В debug-режиме выводится информация об исключении');

    $this->assertEquals($exp, (string)$t, 'Приведение к строке с отладкой');

    $exp = 'CMSx\Template\Exception: ['.Exception::NOT_EXISTS.'] Шаблон "one.php" не существует';
    $this->assertEquals($exp, (string)new Template('one.php'), 'Несуществующий шаблон');

    $exp = 'CMSx\Template\Exception: ['.Exception::NO_TEMPLATE.'] Шаблон не задан';
    $this->assertEquals($exp, (string)new Template, 'Шаблон не выбран');
  }

  protected function setUp()
  {
    $this->path = realpath(__DIR__ . '/../tmpl');
    Template::SetPath($this->path);
    Template::EnableDebug(false);
  }
}