<?php

require_once 'tests/fixtures/MissingAllPromisesAction.php';
require_once 'tests/fixtures/MissingSomePromisesAction.php';
require_once 'tests/fixtures/NextActionAction.php';
require_once 'tests/fixtures/NoExecutedFunctionAction.php';
require_once 'tests/fixtures/NoMissingExpectsAction.php';
require_once 'tests/fixtures/NoMissingPromisesAction.php';
require_once 'tests/fixtures/SuccessfulAction.php';
require_once 'tests/fixtures/UnexpectedErrorAction.php';

it('can be instantiated with an associated array as context', function() {
    $action = new SuccessfulAction(['a' => 1, 'b' => 2]);

    expect($action->context()->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('can be instantiated with an ActionContext as context', function() {
    $action_context = new ActionContext(['a' => 1, 'b' => 2]);
    $action         = new SuccessfulAction($action_context);

    expect($action->context()->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('returns no context validation errors with empty expected keys', function() {
    $result = NoMissingExpectsAction::execute(['a' => 1, 'b' => 2]);

    expect($result->success())->toBeTrue();
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

it('can mark the current context as failed with a message using the fail method', function() {
    $result = NextActionAction::execute(['a' => 1, 'b' => 2]);

    expect($result->failure())->toBeFalse();
    expect($result->success())->toBeTrue();
    expect($result->keys())->not()->toHaveKey('d');
});
