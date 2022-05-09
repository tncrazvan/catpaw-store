<?php

namespace CatPaw\Store;


use Catpaw\Store\Readable;
use Catpaw\Store\Writable;
use Closure;
use Generator;

/**
 * @template T
 * @param mixed $value
 * @return Writable<T>
 */
function writable(mixed $value): Writable {
	return new Writable($value);
}

/**
 * @template T
 * @param mixed   $value
 * @param Closure $start
 * @return Readable
 */
function readable(mixed $value, Closure $start): Readable {
	return new Readable($value, $start);
}