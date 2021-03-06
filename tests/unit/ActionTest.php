<?php

use PHPUnit\Framework\TestCase;

use LightService\Context;

use LightService\Exception\ExpectedKeysNotInContextException;
use LightService\Exception\PromisedKeysNotInContextException;
use LightService\Exception\NotImplementedException;

use LightService\Fixtures\Actions\FailAndReturnAction;
use LightService\Fixtures\Actions\FailingAction;
use LightService\Fixtures\Actions\MissingAllPromisesAction;
use LightService\Fixtures\Actions\MissingSomePromisesAction;
use LightService\Fixtures\Actions\NoExecutedFunctionAction;
use LightService\Fixtures\Actions\NextActionAction;
use LightService\Fixtures\Actions\NoMissingExpectsAction;
use LightService\Fixtures\Actions\NoMissingPromisesAction;
use LightService\Fixtures\Actions\RollbackAction;
use LightService\Fixtures\Actions\SingleExpectsAndPromisesAction;
use LightService\Fixtures\Actions\SuccessfulAction;
use LightService\Fixtures\Actions\DuplicateExpectsAction;
use LightService\Fixtures\Actions\DuplicatePromisesAction;

final class ActionTest extends TestCase {
    public function test_it_can_be_instantiated_with_an_associated_array_as_context() {
        $action = new SuccessfulAction(['a' => 1, 'b' => 2]);

        $this->assertEquals(['a' => 1, 'b' => 2], $action->context()->to_array());
    }

    public function test_it_can_be_instantiated_with_a_given_context_as_action_context() {
        $action_context = new Context(['a' => 1, 'b' => 2]);
        $action         = new SuccessfulAction($action_context);

        $this->assertEquals(['a' => 1, 'b' => 2], $action->context()->to_array());
    }

    public function test_it_instantiates_the_action_context_with_the_class_of_the_action() {
        $action_context = new Context();
        $action         = new SuccessfulAction($action_context);

        $this->assertEquals(SuccessfulAction::class, $action->context()->current_action());
    }

    public function test_it_returns_no_context_validation_errors_with_empty_expected_keys() {
        $result = NoMissingExpectsAction::execute(['a' => 1, 'b' => 2]);

        $this->assertTrue($result->success());
    }

    public function test_it_can_accept_a_single_string_for_the_expected_key_in_the_context() {
        $result = SingleExpectsAndPromisesAction::execute(['a' => 1]);

        $this->assertEquals(2, $result->b);
    }

    public function test_it_throws_an_exception_when_all_of_the_expected_keys_are_not_in_the_context() {
        $this->expectException(ExpectedKeysNotInContextException::class);

        SuccessfulAction::execute([]);
    }

    public function test_it_throws_an_exception_some_of_the_expected_keys_are_not_in_the_context() {
        $this->expectException(ExpectedKeysNotInContextException::class);

        SuccessfulAction::execute(['a' => 1]);
    }

    public function test_it_returns_no_context_validation_exceptions_with_empty_promised_keys() {
        $result = NoMissingPromisesAction::execute(['a' => 1, 'b' => 2]);

        $this->assertTrue($result->success());
    }

    public function test_it_throws_an_exception_when_all_of_the_the_promised_keys_are_not_in_the_context() {
        $this->expectException(PromisedKeysNotInContextException::class);

        MissingAllPromisesAction::execute(['a' => 1, 'b' => 2]);
    }

    public function test_it_throws_an_exception_some_of_the_the_promised_keys_are_not_in_the_context() {
        $this->expectException(PromisedKeysNotInContextException::class);

        MissingSomePromisesAction::execute(['a' => 1, 'b' => 2]);
    }

    public function test_it_throws_an_exception_when_the_executed_function_is_not_implemented() {
        $this->expectException(NotImplementedException::class);

        NoExecutedFunctionAction::execute(['a' => 1, 'b' => 2]);
    }

    public function test_it_can_skip_to_the_next_action_using_the_next_context_function() {
        $result = NextActionAction::execute(['a' => 1, 'b' => 2]);

        $this->assertFalse($result->failure());
        $this->assertTrue($result->success());
        $this->assertArrayNotHasKey('d', $result->keys());
    }

    public function test_it_can_mark_the_current_context_as_failed_with_a_message_using_the_fail_function() {
        $result = FailingAction::execute(['a' => 1, 'b' => 2]);

        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function test_it_can_mark_the_current_context_as_failed_and_move_onto_the_next_context_using_the_fail_and_return_function() {
        $result = FailAndReturnAction::execute();

        $this->assertTrue($result->failure());
        $this->assertArrayNotHasKey('one', $result->keys());
    }

    public function test_it_can_get_the_current_context() {
        $action = new SuccessfulAction(['a' => 1]);

        $this->assertEquals(['a' => 1], $action->context()->to_array());
    }

    public function test_it_can_get_the_expected_keys() {
        $action = new SuccessfulAction(['a' => 1]);

        $this->assertEquals(['a', 'b'], $action->expected_keys());
    }

    public function test_it_can_fail_the_context_and_rollback() {
        $result = RollbackAction::execute(['number' => 1]);

        $this->assertEquals(['number' => 0], $result->to_array());
        $this->assertEquals('I want to roll back!', $result->message());
    }

    public function test_it_can_fail_the_context_and_rollback_statically_with_a_given_context() {
        $result = RollbackAction::rollback(['number' => 1]);

        $this->assertEquals(['number' => 0], $result->to_array());
    }

    public function test_it_does_nothing_when_no_rollback_function_is_defined() {
        $result = SuccessfulAction::rollback(['a' => 1]);

        $this->assertEquals(['a' => 1], $result->to_array());
    }

    public function test_it_ignores_duplicate_expects_keys() {
        $result = DuplicateExpectsAction::execute(['number' => 0]);

        $this->assertEquals(['number' => 1], $result->to_array());
    }

    public function test_it_ignores_duplicate_promises_keys() {
        $result = DuplicatePromisesAction::execute(['number' => 0]);

        $this->assertEquals(['number' => 1], $result->to_array());
    }
}
