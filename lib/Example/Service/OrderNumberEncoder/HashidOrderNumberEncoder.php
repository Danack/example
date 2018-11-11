<?php

declare(strict_types=1);

namespace Example\Service\OrderNumberEncoder;

use Example\Model\CustomerOrder;
use Hashids\Hashids;

class HashidOrderNumberEncoder implements OrderNumberEncoder
{
    public function encode(CustomerOrder $customerOrder) : string
    {
        $hashids = $this->createHashids();

        return $hashids->encode($customerOrder->getId());
    }

    public function decode(string $orderNumber): ?int
    {
        $hashids = $this->createHashids();

        $orderId = $hashids->decode($orderNumber);

        if (count($orderId) === 1) {
            return $orderId[0];
        }

        return null;
    }

    private function createHashids() : Hashids
    {
        return new Hashids('', 6, 'abcdefghjklmopqrstuvwxyz'); // all lowercase
    }
}
