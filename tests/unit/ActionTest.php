<?php

require_once 'tests/fixtures/actions/MissingAllPromisesAction.php';
require_once 'tests/fixtures/actions/MissingSomePromisesAction.php';
require_once 'tests/fixtures/actions/NextActionAction.php';
require_once 'tests/fixtures/actions/NoExecutedFunctionAction.php';
require_once 'tests/fixtures/actions/NoMissingExpectsAction.php';
require_once 'tests/fixtures/actions/NoMissingPromisesAction.php';
require_once 'tests/fixtures/actions/SuccessfulAction.php';
require_once 'tests/fixtures/actions/UnexpectedErrorAction.php';
require_once 'tests/fixtures/actions/FailAndReturnAction.php';
require_once 'tests/fixtures/actions/SingleExpectsAndPromisesAction.php';

it('can be instantiated with an associated array as context', function() {
    $action = new SuccessfulAction(['a' => 1, 'b' => 2]);

    expect($action->context()->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('can be instantiated with an ActionContext as context', function() {
    $action_context = new ActionContext(['a' => 1, 'b' => 2]);
    $action         = new SuccessfulAction($action_context);

    expect($action->context()->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('instantiates the action context with the class of the action', function() {
    $action_context = new ActionContext();
    $action         = new SuccessfulAction($action_context);

    expect($action->context()->current_action())->toEqual(SuccessfulAction::class);
});

it('returns no context validation errors with empty expected keys', function() {
    $result = NoMissingExpectsAction::execute(['a' => 1, 'b' => 2]);

    expect($result->success())->toBeTrue();
});

it('can accept a single string for the expected key in the context', function() {
    $result = SingleExpectsAndPromisesAction::execute(['a' => 1]);

    expect($result->b)->toEqual(2);
});

it('throws an exception when all of the expected keys are not in the context', function() {
    SuccessfulAction::execute([]);
})->throws(ExpectedKeysNotInContextException::class);

it('throws an exception some of the expected keys are not in the context', function() {
    SuccessfulAction::execute(['a' => 1]);
})->throws(ExpectedKeysNotInContextException::class);

it('returns no context validation exceptions with empty promised keys', function() {
    $result = NoMissingPromisesAction::execute(['a' => 1, 'b' => 2]);

    expect($result->success())->toBeTrue();
});

it('throws an exception when all of the the promised keys are not in the context', function() {
    MissingAllPromisesAction::execute(['a' => 1, 'b' => 2]);
})->throws(PromisedKeysNotInContextException::class);

it('throws an exception some of the the promised keys are not in the context', function() {
    MissingSomePromisesAction::execute(['a' => 1, 'b' => 2]);
})->throws(PromisedKeysNotInContextException::class);

it('throws an exception when the executed function is not implemented', function() {
    NoExecutedFunctionAction::execute(['a' => 1, 'b' => 2]);
})->throws(NotImplementedException::class);

it('can skip to the next action using the next_context function', function() {
    $result = NextActionAction::execute(['a' => 1, 'b' => 2]);

    expect($result->failure())->toBeFalse();
    expect($result->success())->toBeTrue();
    expect($result->keys())->not()->toHaveKey('d');
});

it('can mark the current context as failed with a message using the fail function', function() {
    $result = FailingAction::execute(['a' => 1, 'b' => 2]);

    expect($result->failure())->toBeTrue();
    expect($result->success())->toBeFalse();
});

it('can mark the current context as failed and move onto the next context using the fail_and_return function', function() {
    $result = FailAndReturnAction::execute();

    expect($result->failure())->toBeTrue();
    expect($result->keys())->not()->toHaveKey('one');
});
