<?php

declare(strict_types=1);

namespace Example\Service\OrderNumberEncoder;

use Example\Model\CustomerOrder;

interface OrderNumberEncoder
{
    public function encode(CustomerOrder $customerOrder) : string;

    public function decode(string $orderNumber) : ?int;
}
