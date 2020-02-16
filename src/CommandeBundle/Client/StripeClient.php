<?php


namespace CommandeBundle\Client;





class StripeClient
{


    public function createIntent()
    {


        \Stripe\Stripe::setApiKey('sk_test_UAe411cwz5wIeEJMQW8cXSgs00whcLY6Pw');

        $intent = \Stripe\PaymentIntent::create([
            'amount' => 1099,
            'currency' => 'usd',
        ]);

        return $intent;
    }
}