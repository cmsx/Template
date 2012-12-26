<?php

namespace CMSx\Template;

class Exception extends \Exception
{
  /** Шаблон не указан */
  const NO_TEMPLATE = 1;
  /** Шаблон не существует */
  const NOT_EXISTS = 2;

  protected static $errors = array(
    self::NO_TEMPLATE => 'Шаблон не задан',
    self::NOT_EXISTS  => 'Шаблон "%s" не существует'
  );

  /** @throws Exception */
  public static function NotExists($tmpl)
  {
    throw new static(sprintf(self::$errors[self::NOT_EXISTS], $tmpl), self::NOT_EXISTS);
  }

  /** @throws Exception */
  public static function NoTemplate()
  {
    throw new static(self::$errors[self::NO_TEMPLATE], self::NO_TEMPLATE);
  }
}