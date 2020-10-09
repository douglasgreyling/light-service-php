<?php

require_once 'src/classes/ActionContext.php';

it('is instantiated with an empty context when given no state', function() {
    $context = new ActionContext();

    expect($context->to_array())->toBeEmpty();
});

it('is instantiated with a context based on its given state', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('is instantiated with a context failure flag of false', function() {
    $context = new ActionContext();

    expect($context->failure())->toBeFalse();
});

it('is instantiated with a context failure flag of true', function() {
    $context = new ActionContext();

    expect($context->success())->toBeTrue();
});

it('can retrieve values like an array', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context['a'])->toEqual(1);
});

it('can set values like an array', function() {
    $context = new ActionContext();

    $context['a'] = 1;

    expect($context['a'])->toEqual(1);
});

it('returns a value of null when the key being fetched does not exist in the context', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->get('b'))->toBeNull();
});

it('can retrieve values using the get method', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->get('a'))->toEqual(1);
});

it('returns a value of null when the key passed to the get method does not exist in the context', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->get('b'))->toBeNull();
});

it('can set values using the set method', function() {
    $context = new ActionContext();

    $context->set('a', 1);

    expect($context->to_array())->toEqual(['a' => 1]);
});

it('can get values like properties of an object', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->a)->toEqual(1);
});

it('can set values like properties of an object', function() {
    $context = new ActionContext();

    $context->a = 1;

    expect($context->a)->toEqual(1);
});

it('returns a value of null when the property being fetched does not exist in the context', function() {
    $context = new ActionContext(['a' => 1]);

    expect($context->b)->toBeNull();
});

it('can merge a set of key/value pairs into the context using the merge method', function() {
    $context = new ActionContext(['a' => 1]);

    $context->merge(['b' => 2, 'c' => 3]);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('can merge a set of key/value pairs into the context using the array merge method', function() {
    $context = new ActionContext(['a' => 1]);

    $context->array_merge(['b' => 2, 'c' => 3]);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2, 'c' => 3]);
});

it('can retrieve the keys inside the context with the keys method', function () {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->keys())->toEqual(['a', 'b']);
});

it('can retrieve the values inside the context with the values method', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->values())->toEqual([1, 2]);
});

it('can return the current context as an array', function() {
    $state   = ['a' => 1, 'b' => 2];
    $context = new ActionContext($state);

    expect($context->to_array())->toEqual(['a' => 1, 'b' => 2]);
});

it('can retrieve multiple key/value pairs using the fetch method', function() {
    $state   = ['a' => 1, 'b' => 2, 'c' => 3];
    $context = new ActionContext($state);

    expect($context->fetch(['a', 'c']))->toEqual(['a' => 1, 'c' => 3]);
});