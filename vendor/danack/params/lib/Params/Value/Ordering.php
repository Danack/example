<?php

declare(strict_types=1);

namespace Params\Value;

/**
 * Class Ordering
 *
 * Represents a set of OrderingElements.
 *
 * e.g. for query where the user wanted to sort by name ascending, and then date
 * descending, they would pass the parameter as "+name,-date" which would be parsed
 * into two OrderElements of:
 *
 * new OrderElement('name', Ordering::ASC)
 * new OrderElement('date', Ordering::DESC)
 *
 */
class Ordering
{
    const ASC = 'asc';
    const DESC = 'desc';

    /** @return \Params\Value\OrderElement[] */
    private $orderElements;

    /**
     * Order constructor.
     * @param \Params\Value\OrderElement[] $orderElements
     */
    public function __construct(array $orderElements)
    {
        $this->orderElements = $orderElements;
    }

    /**
     * @return \Params\Value\OrderElement[]
     */
    public function getOrderElements()
    {
        return $this->orderElements;
    }

    /**
     * @return string[]
     */
    public function toOrderArray()
    {
        $fn = function ($carry, \Params\Value\OrderElement $orderElement) {
            $carry[$orderElement->getName()] = $orderElement->getOrder();
            return $carry;
        };

        return array_reduce($this->orderElements, $fn, []);
    }
}
