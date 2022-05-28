<?php

namespace Tests;

use function Amp\delay;
use Amp\PHPUnit\AsyncTestCase;

use function CatPaw\Store\readable;

class ReadableTest extends AsyncTestCase {
    public function testCreationAnSubscribe() {
        $this->setTimeout(4000);
        $store = readable("hello", function($set) {
            yield delay(1000);
            $set("hello world");
            return function() { };
        });

        
        $unsubscribe = $store->subscribe(fn($value) => $this->assertEquals("hello", $value));
        $unsubscribe();

        yield delay(2000);
        $unsubscribe = $store->subscribe(fn($value) => $this->assertEquals("hello world", $value));
        $unsubscribe();
    }
}