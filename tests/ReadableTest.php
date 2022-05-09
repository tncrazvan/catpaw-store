<?php

namespace Tests;

use Amp\Loop;
use PHPUnit\Framework\TestCase;
use function Amp\delay;
use function CatPaw\Store\readable;

class ReadableTest extends TestCase {

	public function testCreationAnSubscribe() {
		Loop::run(function() {
			$store = readable("hello", function($set) {
				delay(1000)->onResolve(function() use ($set) {
					$set("hello world");
				});

				return function() { };
			});

			$unsubscribe = $store->subscribe(fn($value) => $this->assertEquals("hello", $value));
			$unsubscribe();


			delay(2000)->onResolve(function() use ($store) {
				$unsubscribe = $store->subscribe(fn($value) => $this->assertEquals("hello world", $value));
				$unsubscribe();
			});

		});
	}
}