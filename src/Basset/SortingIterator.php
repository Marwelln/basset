<?php
// https://github.com/salathe/spl-examples/wiki/Sorting-Iterators

namespace Basset;

use ArrayIterator;

class SortingIterator extends ArrayIterator {
	public function __construct($iterator, $callback) {
		if ( ! is_callable($callback)) {
			throw new InvalidArgumentException(sprintf('Callback must be callable (%s given).', $callback));
		}

		parent::__construct(iterator_to_array($iterator));
		$this->uasort($callback);
	}
}