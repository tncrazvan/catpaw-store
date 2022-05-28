<?php

namespace Tests;

use function Amp\delay;
use Amp\PHPUnit\AsyncTestCase;

use function CatPaw\Store\writable;

class WritableTest extends AsyncTestCase {
    public function testSet() {
        $store = writable("hello");
        $this->assertEquals("hello", $store->get());
        $store->set("hello world");
        $this->assertEquals("hello world", $store->get());
    }

    public function testSubscribe() {
        $this->setTimeout(2000);
        $startTime = time();
        yield delay(1000);
        $store = writable(time());
        $store->subscribe(fn($now) => $this->assertGreaterThan($startTime, $now));
        $store->set(time());
    }
}