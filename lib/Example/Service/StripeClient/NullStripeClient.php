<?php

declare(strict_types = 1);

namespace Example\Service\StripeClient;

class NullStripeClient implements StripeClient
{
    public function createOneOffPayment(
        int $amount,
        string $currency,
        string $token_id,
        string $token_email,
        string $description,
        array $stripe_args
    ): \Stripe\Charge {
        throw new \Exception("createOneOffPayment not implemented yet.");
    }
}
