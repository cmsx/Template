<?php

namespace CMSx;

use CMSx\Container;
use CMSx\Template\Exception;

class Template extends Container
{
  /** Шаблон */
  protected $template;
  /** Временное значение шаблона */
  protected $cur_template;
  /** @var \Exception */
  protected $exception;
  /** Нужно ли экранировать Спецсимволы */
  protected $escape = true;

  /** Режим отладки */
  protected static $debug;
  /** Папка с шаблонами */
  protected static $dir;
  /** Кеш проверки существования шаблонов */
  protected static $tmpl_exist_arr = array();

  /**
   * @param null  $template Путь к шаблону
   * @param array $vars Переменные в шаблоне
   * @param bool  $escape Нужно ли экранировать переменные, по-умолчанию - да
   */
  function __construct($template = null, array $vars = null, $escape = null)
  {
    if ($template) {
      $this->setTemplate($template);
    }
    if ($vars) {
      $this->fromArray($vars);
    }
    if (!is_null($escape)) {
      $this->enableEscaping($escape);
    }
  }

  function __toString()
  {
    return $this->render();
  }

  /** Отрисовка шаблона */
  public function render($template = null)
  {
    $this->exception = null;

    if (is_null($template)) {
      $template = $this->template;
    }

    //Защищаем от затирания extract`ом
    $this->cur_template = $template;

    try {
      if ($this->cur_template) {
        ob_start();
        extract($this->getVars(), EXTR_OVERWRITE);

        include $this->getTemplatePath($this->cur_template);

        return ob_get_clean();
      } else {
        Exception::NoTemplate();
      }
    } catch (\Exception $e) {
      $this->exception = $e;

      if (self::$debug) {
        return sprintf('%s: [%s] %s', get_class($e), $e->getCode(), $e->getMessage());
      }

      return '';
    }
  }

  /** Файл шаблона */
  public function setTemplate($template)
  {
    $this->template = $template;

    return $this;
  }

  /** Файл шаблона */
  public function getTemplate()
  {
    return $this->template;
  }

  /**
   * Если в шаблоне были исключения - возвращает объект исключения
   * @return \Exception
   */
  public function getException()
  {
    return $this->exception ? : false;
  }

  /** Нужно ли экранировать вывод переменных в шаблоне */
  public function enableEscaping($on = true)
  {
    $this->escape = $on;

    return $this;
  }

  /** Путь к папке с шаблонами. Правый слеш отсекается */
  public static function SetPath($dir)
  {
    self::$dir = rtrim($dir, DIRECTORY_SEPARATOR);
  }

  /** Путь к папке с шаблонами */
  public static function GetPath()
  {
    return self::$dir ? : __DIR__;
  }

  /** Включение режима отладки - выводится информация о ошибках */
  public static function EnableDebug($enable = true)
  {
    self::$debug = $enable;
  }

  /** Проверка существования шаблона */
  public static function CheckTemplateExists($tmpl)
  {
    $n = md5($tmpl);
    if (!isset(self::$tmpl_exist_arr[$n])) {
      self::$tmpl_exist_arr[$n] = is_file(self::GetPathToTemplate($tmpl));
    }

    return self::$tmpl_exist_arr[$n];
  }

  /**
   * Полный путь к шаблону
   * Если шаблон не существует - возвращает false
   */
  public static function GetPathToTemplate($tmpl)
  {
    return self::GetPath() . DIRECTORY_SEPARATOR . ltrim($tmpl, DIRECTORY_SEPARATOR);
  }

  /** Получение переменных для экспорта в шаблон */
  protected function getVars()
  {
    if (!$v = $this->vars) {
      return array();
    }

    if ($this->escape) {
      array_walk($v, function(&$var){if (is_string($var)) {$var = htmlspecialchars($var);}});
    }

    return $v;
  }

  /** Проверка существования шаблона и получние полного пути */
  protected function getTemplatePath($template)
  {
    if (!self::CheckTemplateExists($template)) {
      Exception::NotExists($template);
    }

    return self::GetPathToTemplate($template);
  }
}