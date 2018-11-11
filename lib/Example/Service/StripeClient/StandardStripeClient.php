<?php

namespace Example\Service\StripeClient;

use Example\Service\StripeClient\StripeClient;
use Example\StripeConfig;
use Stripe\Error\Base as StripeException;
use Example\Exception\Payment\OneOffPaymentException;

class StandardStripeClient implements StripeClient
{
    /** @var \Example\StripeConfig */
    private $stripeConfig;

    public function __construct(StripeConfig $stripeConfig)
    {
        $this->stripeConfig = $stripeConfig;
    }

    public function createOneOffPayment(
        int $amount,
        string $currency,
        string $token_id,
        string $token_email,
        string $description,
        array $stripe_args
    ) : \Stripe\Charge {
        \Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
        try {
            $customer = \Stripe\Customer::create([
                "email" => $token_email,
                "source" => $token_id
            ]);
        }
        catch (StripeException $stripeException) {
            throw new OneOffPaymentException("Failed to create createOneOffPayment ", 0, $stripeException);
        }

        try {
            $this->updateCustomerCardDetailsWithStripeArgs($customer, $token_id, $stripe_args);

            $params = [
                'amount' => $amount,
                'currency' => $currency,
                'description' => $description,
                "customer" => $customer->id,
            ];
            $charge = \Stripe\Charge::create($params);

            return $charge;
        }
        catch (StripeException $stripeException) {
            throw new OneOffPaymentException("Failed to create createOneOffPayment ", 0, $stripeException);
        }
    }


    private function updateCustomerCardDetailsWithStripeArgs(\Stripe\Customer $customer, $token_id, $stripe_args)
    {
        if (!$stripe_args) {
            return;
        }

        $stripe_args_json = json_decode_safe($stripe_args);

        $token = \Stripe\Token::retrieve($token_id);
        $card = $customer->sources->retrieve($token->card->id);

        if (array_key_exists('billing_name', $stripe_args_json) && $stripe_args_json['billing_name'] !== '') {
            $card->name = $stripe_args_json['billing_name'];
        }
        if (array_key_exists('billing_address_city', $stripe_args_json) && $stripe_args_json['billing_address_city'] !== '') {
            $card->address_city = $stripe_args_json['billing_address_city'];
        }
        if (array_key_exists('billing_address_country', $stripe_args_json) && $stripe_args_json['billing_address_country'] !== '') {
            $card->address_country = $stripe_args_json['billing_address_country'];
        }
        if (array_key_exists('billing_address_line1', $stripe_args_json) && $stripe_args_json['billing_address_line1'] !== '') {
            $card->address_line1 = $stripe_args_json['billing_address_line1'];
        }
        if (array_key_exists('billing_address_zip', $stripe_args_json) && $stripe_args_json['billing_address_zip'] !== '') {
            $card->address_zip = $stripe_args_json['billing_address_zip'];
        }
        $card->save();
    }
}
