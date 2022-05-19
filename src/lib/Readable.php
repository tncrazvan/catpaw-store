<?php

namespace Catpaw\Store;

use function Amp\call;
use Amp\LazyPromise;
use Closure;
use Generator;
use SplDoublyLinkedList;

/**
 * @template T
 */
class Readable {

    /** @var SplDoublyLinkedList<Closure> */
    protected SplDoublyLinkedList $callbacks;
    private false|Closure         $stop = false;
    private bool                  $firstSubscriber = true;

    public function __construct(
		protected mixed $value,
		private Closure $onStart,
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
    private function set(mixed $value): void {
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
        if ($this->firstSubscriber) {
            $this->firstSubscriber = false;
            //initiate the store and retrieve the stop function for later use
            $this->stop = (($this->onStart)(fn($value) => $this->set($value))) ?? false;
        }

		($callback)($this->value);

        return fn() => $this->unsubscribe($callback);
    }

    private function unsubscribe(Closure $callback) {
        for ($this->callbacks->rewind(); $this->callbacks->valid(); $this->callbacks->next()) {
            if ($this->callbacks->current() === $callback) {
                $this->callbacks->offsetUnset($this->callbacks->key());
                if (0 === $this->callbacks->count()) {
                    if ($this->stop) {
                        ($this->stop)();
                    }
                    $this->firstSubscriber = true;
                }
                return;
            }
        }
    }
}