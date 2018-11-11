<?php

namespace Example\Service\StripeClient;

interface StripeClient
{
    public function createOneOffPayment(
        int $amount,
        string $currency,
        string $token_id,
        string $token_email,
        string $description,
        array $stripe_args
    ) : \Stripe\Charge;
}
