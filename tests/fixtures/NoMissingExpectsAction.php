<?php

require_once 'src/Action.php';

class NoMissingExpectsAction {
  use LightServicePHP\Action;

  private static function promises() {
    return ['c'];
  }

  public static function executed($context) {
    $a = $context['a'];
    $b = $context['b'];

    $c = self::adds($a, $b);

    $context['c'] = $c;
  }

  private static function adds($a, $b) {
    return $a + $b;
  }
}