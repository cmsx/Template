Компонент Template
==================

В качестве синтаксиса шаблонов используется нативный PHP.

Объект шаблона расширяет класс контейнера значений ([CMSx\Container](https://github.com/cmsx/Container)).

Для использования шаблонов предварительно необходимо указать полный путь к папке с шаблонами: `Template::SetPath($path)`, после чего в шаблонах указывается относительный путь.

## Пример использования

hello.php - файл шаблона:

    <h1><?= $hello ?></h1>

Вызов шаблона:

    $t = new \CMSx\Template('hello.php'); //Предполагается что путь к шаблонам указан ранее
    $t->set('hello', 'World');
    echo $t->render(); //Получим <h1>World</h1>

Для удобства использования шаблон реализует метод `__toString`, что позволяет сократить вызов из примера до `echo $t;`.

В шаблоне доступ к текущему объекту шаблона возможен через переменную `$this`. Таким образом, если в шаблоне нужна какая-либо логика, можно сделать класс-наследник шаблона, реализовать нужный метод и обращаться к нему из шаблона `$this->myMethod()`. Аналогично, вызов `<?= $hello ?>` можно заменить на `<?= $this->get('hello') ?>`

## Ошибки в шаблонах
Если в процессе выполнения шаблона выбрасывается Exception, он перехватывается. Доступ к исключению можно получить с помощью `getException()`.

При разработке полезно сразу видеть отладочную информацию, поэтому можно включить режим `Template::EnableDebug($on)`, тогда при возникновении ошибки внутри шаблона, вместо содержимого шаблона будет выведена информация об ошибке.