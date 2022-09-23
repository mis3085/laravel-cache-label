<?php

require 'CacheLabelTestSet.php';

beforeEach(function () {
    cache()->setDefaultDriver('array');
    cache()->flush();
    $this->labels = ['label-1', 'label-2', 'label-3'];
    $this->cacheWithLabels = cache()->labels($this->labels);
});
