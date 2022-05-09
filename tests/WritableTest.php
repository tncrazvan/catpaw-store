<?php

namespace Tests;

use Amp\Loop;
use PHPUnit\Framework\TestCase;
use function Amp\delay;
use function CatPaw\Store\writable;

class WritableTest extends TestCase {

	public function testSet() {
		Loop::run(function() {
			$store = writable("hello");
			$this->assertEquals("hello", $store->get());
			$store->set("hello world");
			$this->assertEquals("hello world", $store->get());
		});
	}

	public function testSubscribe() {
		Loop::run(function() {
			$startTime = time();
			yield delay(1000);
			$store = writable(time());
			$store->subscribe(fn($now) => $this->assertGreaterThan($startTime, $now));
			$store->set(time());
		});
	}
}