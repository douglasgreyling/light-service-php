<?php

require_once 'src/Action.php';

class NextActionAction {
    use LightServicePHP\Action;

    private function executed($context) {
        $this->next_context();

        $context->d = 5;
    }

    private function adds($a, $b) {
        return $a + $b;
    }
}