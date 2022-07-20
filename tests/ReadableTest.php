<?php

namespace Tests;

use function Amp\delay;

use Amp\Loop;
use function CatPaw\Store\readable;

use PHPUnit\Framework\TestCase;

class ReadableTest extends TestCase {
    public function testCreationAnSubscribe() {
        Loop::run(function() {
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
        });
    }
}