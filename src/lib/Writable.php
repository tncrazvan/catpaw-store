<?php

namespace Catpaw\Store;

use Closure;
use SplDoublyLinkedList;

/**
 * @template T
 */
class Writable {

    /** @var SplDoublyLinkedList<Closure> */
    protected SplDoublyLinkedList $callbacks;

    public function __construct(
        protected mixed $value
    ) {
        $this->callbacks = new SplDoublyLinkedList();
        $this->callbacks->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_KEEP);
    }

    /**
     * Get the value of the store.
     * @return T
     */
    public function get(): mixed {
        return $this->value;
    }

    /**
     * Set the value of the store.
     * @param  T    $value
     * @return void
     */
    public function set(mixed $value): void {
        $this->value = $value;
        for ($this->callbacks->rewind(); $this->callbacks->valid(); $this->callbacks->next()) {
            $this->callbacks->current()($this->value);
        }
    }


    /**
     * Subscribe to this store and get notified of every update.
     * @param  Closure $callback callback executed whenever there's an update,
     *                           it takes 1 parameter, the new value of the store.
     * @return Closure a function that cancels this subscriptions.
     */
    public function subscribe(Closure $callback): Closure {
        $this->callbacks->push($callback);

        ($callback)($this->value);

        return fn() => $this->unsubscribe($callback);
    }

    private function unsubscribe(Closure $callback) {
        for ($this->callbacks->rewind(); $this->callbacks->valid(); $this->callbacks->next()) {
            if ($this->callbacks->current() === $callback) {
                $this->callbacks->offsetUnset($this->callbacks->key());
                return;
            }
        }
    }
}