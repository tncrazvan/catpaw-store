<?php

namespace CatPaw\Store;

use Catpaw\Store\Readable;
use Catpaw\Store\Writable;
use Closure;

/**
 * @param  mixed    $value
 * @return Writable
 */
function writable(mixed $value): Writable {
    return new Writable($value);
}

/**
 * @param  mixed    $value
 * @param  Closure  $start
 * @return Readable
 */
function readable(mixed $value, Closure $start): Readable {
    return new Readable($value, $start);
}