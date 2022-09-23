<?php

test('item can be set with labels', function () {
    $itemKey = 'put-1';
    $itemValue = 'value-1';
    $seconds = 10;

    expect($this->cacheWithLabels->put($itemKey, $itemValue, $seconds))->toBeTrue();
    expect($this->cacheWithLabels->get($itemKey))->toBe($itemValue);
});

test('item can be incremented with labels', function () {
    $itemKey = 'increment-1';
    $itemValue = 1;

    $this->cacheWithLabels->put($itemKey, $itemValue, 10);

    expect($this->cacheWithLabels->increment($itemKey, 1))->toBe(2);
    expect($this->cacheWithLabels->get($itemKey))->toBe(2);

    expect($this->cacheWithLabels->increment($itemKey, 2))->toBe(4);
    expect($this->cacheWithLabels->get($itemKey))->toBe(4);

    expect(cache()->increment($itemKey, 3))->toBe(7);
    expect($this->cacheWithLabels->get($itemKey))->toBe(7);
});

test('item can be decremented with labels', function () {
    $itemKey = 'decrement-1';
    $itemValue = 10;

    $this->cacheWithLabels->put($itemKey, $itemValue, 10);

    expect($this->cacheWithLabels->decrement($itemKey, 1))->toBe(9);
    expect($this->cacheWithLabels->get($itemKey))->toBe(9);

    expect($this->cacheWithLabels->decrement($itemKey, 2))->toBe(7);
    expect($this->cacheWithLabels->get($itemKey))->toBe(7);

    expect(cache()->decrement($itemKey, 3))->toBe(4);
    expect($this->cacheWithLabels->get($itemKey))->toBe(4);
});

test('item with labels can be removed but the labels would not forget the item key', function () {
    $itemKey = 'forget-1';
    $itemValue = 1;
    $this->cacheWithLabels->put($itemKey, $itemValue, 10);

    cache()->forget($itemKey);

    expect($this->cacheWithLabels->get($itemKey))->toBeNull();

    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey($itemKey))->toBe(true);
    }
});

test('removed item with labels can be renewed and the labels can get the renewed value', function () {
    $itemKey = 'forget-1';
    $itemValue = 1;

    $this->cacheWithLabels->put($itemKey, $itemValue, 10);

    cache()->forget($itemKey);

    cache()->put($itemKey, $itemValue + 1);

    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->get($itemKey))->toBe($itemValue + 1);
    }
});

test('item can be retrieved with labels and the order of the labels does not matter', function () {
    $itemKey = 'get-1';
    $itemValue = 'value-1';

    $this->cacheWithLabels->put($itemKey, $itemValue, 10);

    expect(cache()->get($itemKey))->toBe($itemValue);
    expect(cache()->labels(array_reverse($this->labels))->get($itemKey))->toBe($itemValue);
    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->get($itemKey))->toBe($itemValue);
    }
});

test('item can be retrieved only when it exists in every specified label', function () {
    $itemKey = 'get-1';
    $itemValue = 'value-1';

    $this->cacheWithLabels->put($itemKey, $itemValue, 10);

    $withWrongLabels = array_merge($this->labels, ['wrong-label']);

    expect(cache()->labels($withWrongLabels)->get($itemKey))->toBeNull();
});

test('the default flush behavior with labels is to delete everything from the cache store no matter labeled or not', function () {
    cache()->put('item-no-label', 'foo', 10);
    $this->cacheWithLabels->put('item-has-label', 'bar', 10);

    $this->cacheWithLabels->flush();

    // everything has gone
    expect(cache()->get('item-no-label'))->toBeNull();
    expect($this->cacheWithLabels->get('item-has-label'))->toBeNull();

    // the relation between item and labels has also been erased
    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey('item-has-label'))->toBeFalse();
    }
});

test('the flush behavior with labels can be set to flush_labels', function () {
    config(['cache-label.flush_behavior' => 'flush_labels']);

    cache()->put('item-no-label', 'foo', 10);
    $this->cacheWithLabels->put('item-has-label', 'bar', 10);

    $this->cacheWithLabels->flush();

    // untouched
    expect(cache()->get('item-no-label'))->toBe('foo');

    // deleted but the labels still remember the key of item
    expect($this->cacheWithLabels->get('item-has-label'))->toBeNull();
    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey('item-has-label'))->toBeTrue();
    }
});

test('the flush behavior with labels can be set to reset_labels', function () {
    config(['cache-label.flush_behavior' => 'reset_labels']);

    cache()->put('item-no-label', 'foo', 10);
    $this->cacheWithLabels->put('item-has-label', 'bar', 10);

    $this->cacheWithLabels->flush();

    // untouched
    expect(cache()->get('item-no-label'))->toBe('foo');

    // item still exists in cache store but no longer belongs to any label
    expect(cache()->get('item-has-label'))->toBe('bar');
    expect($this->cacheWithLabels->get('item-has-label'))->toBeNull();
    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey('item-has-label'))->toBeFalse();
    }
});

test('the flush behavior of reset_labels should be scoped', function () {
    config(['cache-label.flush_behavior' => 'reset_labels']);

    $affected = ['affected-1', 'affected-2', 'affected-3'];
    $unaffected = ['unaffected-4', 'unaffected-5'];
    $all = array_merge($affected, $unaffected);

    cache()->labels($all)->put('foo', 'bar', 10);

    cache()->labels($affected)->flush();

    expect(cache()->labels($unaffected)->get('foo'))->toBe('bar');

    expect(cache()->labels($affected)->get('foo'))->toBeNull();
    foreach ($affected as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey('item-has-label'))->toBeFalse();
    }
});

it('can get itemKeys of a label', function () {
    $this->cacheWithLabels->putMany([
        'foo' => 'foo1',
        'bar' => 'bar1',
    ]);

    expect($this->cacheWithLabels->getLabels()->getItemKeysOfLabel('label-1'))->toBe([
        'foo',
        'bar',
    ]);

    cache()->labels('label-1')->putMany([
        'new-1' => 1,
        'new-2' => 2,
    ]);

    expect($this->cacheWithLabels->getLabels()->getItemKeysOfLabel('label-1'))->toBe([
        'foo',
        'bar',
        'new-1',
        'new-2',
    ]);
});

test('the default behavior of forget() is only to delete the item from cache store but the labels still remember it', function () {
    config(['cache-label.forget_and_remove' => false]);

    $this->cacheWithLabels->put('foo', 'bar', 10);
    $this->cacheWithLabels->forget('foo');

    // item deleted
    expect($this->cacheWithLabels->get('foo'))->toBeNull();
    // labels still remember it
    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey('foo'))->toBeTrue();
    }
});

test('the behavior of forget() can be set to forget and remove', function () {
    config(['cache-label.forget_and_remove' => true]);

    $this->cacheWithLabels->put('foo', 'bar', 10);
    $this->cacheWithLabels->forget('foo');

    // item deleted
    expect($this->cacheWithLabels->get('foo'))->toBeNull();
    // labels also forget it
    foreach ($this->labels as $label) {
        expect(cache()->labels($label)->getLabels()->hasItemKey('foo'))->toBeFalse();
    }
});

test('unlink items from the labels', function () {
    $this->cacheWithLabels->putMany([
        'foo-1' => 'value-1',
        'foo-2' => 'value-2',
        'bar-1' => 'value-3',
        'bar-2' => 'value-4',
    ]);


    $this->cacheWithLabels->unlinkItems('foo-2');
    expect(cache()->get('foo-2'))->toBe('value-2');

    foreach ($this->labels as $label) {
        expect($this->cacheWithLabels->getLabels()->getItemKeysOfLabel($label))->toBe([
            'foo-1',
            'bar-1',
            'bar-2',
        ]);
    }

    $this->cacheWithLabels->unlinkItems(['foo-1', 'bar-2']);
    expect(cache()->getMultiple(['foo-1', 'bar-2']))->toBe([
        'foo-1' => 'value-1',
        'bar-2' => 'value-4',
    ]);
    foreach ($this->labels as $label) {
        expect($this->cacheWithLabels->getLabels()->getItemKeysOfLabel($label))->toBe([
            'bar-1',
        ]);
    }
});

test('unlink items from the labels should be scoped', function () {
    $affected = ['affected-1', 'affected-2', 'affected-3'];
    $unaffected = ['unaffected-4', 'unaffected-5'];
    $all = array_merge($affected, $unaffected);

    cache()->labels($all)->putMany([
        'foo-1' => 'value-1',
        'foo-2' => 'value-2',
        'bar-1' => 'value-3',
        'bar-2' => 'value-4',
    ]);

    cache()->labels($affected)->unlinkItems('foo-2');
    foreach ($affected as $label) {
        expect(cache()->labels($affected)->getLabels()->getItemKeysOfLabel($label))->toBe([
            'foo-1',
            'bar-1',
            'bar-2',
        ]);
    }

    foreach ($unaffected as $label) {
        expect(cache()->labels($unaffected)->getLabels()->getItemKeysOfLabel($label))->toBe([
            'foo-1',
            'foo-2',
            'bar-1',
            'bar-2',
        ]);
    }
});
