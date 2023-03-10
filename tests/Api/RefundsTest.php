<?php

/**
 * Part of the Stripe package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Stripe
 * @version    2.4.6
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2021, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Cartalyst\Stripe\Tests\Api;

use Cartalyst\Stripe\Tests\FunctionalTestCase;
use Cartalyst\Stripe\Exception\NotFoundException;

class RefundsTest extends FunctionalTestCase
{
    /** @test */
    public function it_can_create_a_refund_from_a_charge()
    {
        $customer = $this->createCustomer();

        $charge = $this->createCharge($customer['id']);

        $refund = $this->stripe->refunds()->create($charge['id']);

        $charge = $this->stripe->charges()->find($charge['id']);

        $this->assertTrue($charge['refunded']);
        $this->assertSame($charge['id'], $refund['charge']);
        $this->assertSame('succeeded', $refund['status']);
        $this->assertSame(5049, $refund['amount']);
    }

    /** @test */
    public function it_can_create_a_refund_from_a_payment_intent()
    {
        $customer = $this->createCustomer();

        $this->createCardThroughToken($customer['id']);

        $paymentIntent = $this->stripe->paymentIntents()->create([
            'amount' => 3000,
            'currency' => 'USD',
            'confirm' => true,
            'customer' => $customer['id'],
        ]);

        $refund = $this->stripe->refunds()->create($paymentIntent['id']);

        $this->assertSame($paymentIntent['id'], $refund['payment_intent']);
        $this->assertSame('succeeded', $refund['status']);
        $this->assertSame(300000, $refund['amount']);
    }

    /** @test */
    public function it_can_create_a_partial_refund()
    {
        $customer = $this->createCustomer();

        $charge = $this->createCharge($customer['id']);

        $refund = $this->stripe->refunds()->create($charge['id'], 20.00);

        $charge = $this->stripe->charges()->find($charge['id']);

        $this->assertFalse($charge['refunded']);
        $this->assertSame(2000, $refund['amount']);
    }

    /** @test */
    public function it_can_find_a_refund()
    {
        $customer = $this->createCustomer();

        $charge = $this->createCharge($customer['id']);

        $refund = $this->stripe->refunds()->create($charge['id']);

        $refund = $this->stripe->refunds()->find($charge['id'], $refund['id']);

        $charge = $this->stripe->charges()->find($charge['id']);

        $this->assertTrue($charge['refunded']);
        $this->assertSame(5049, $refund['amount']);
    }

    /** @test */
    public function it_can_find_a_refund_without_passing_the_charge_id()
    {
        $customer = $this->createCustomer();

        $charge = $this->createCharge($customer['id']);

        $refund = $this->stripe->refunds()->create($charge['id']);

        $refund = $this->stripe->refunds()->find($refund['id']);

        $charge = $this->stripe->charges()->find($charge['id']);

        $this->assertTrue($charge['refunded']);
        $this->assertSame(5049, $refund['amount']);
    }

    /** @test */
    public function it_will_throw_an_exception_when_searching_for_a_non_existing_refund()
    {
        $this->expectException(NotFoundException::class);

        $customer = $this->createCustomer();

        $charge = $this->createCharge($customer['id']);

        $this->stripe->refunds()->find($charge['id'], time().rand());
    }

    /** @test */
    public function it_can_update_a_refund()
    {
        $customer = $this->createCustomer();

        $charge = $this->createCharge($customer['id']);

        $refund = $this->stripe->refunds()->create($charge['id']);

        $refund = $this->stripe->refunds()->update($charge['id'], $refund['id'], [
            'metadata' => [ 'reason' => 'Refunded the payment.' ]
        ]);

        $this->assertSame(5049, $refund['amount']);
        $this->assertSame('Refunded the payment.', $refund['metadata']['reason']);
    }

    /** @test */
    public function it_can_retrieve_all_refunds_of_a_charge()
    {
        $customer = $this->createCustomer();

        $charge1 = $this->createCharge($customer['id']);
        $charge2 = $this->createCharge($customer['id']);

        $this->stripe->refunds()->create($charge1['id']);
        $this->stripe->refunds()->create($charge2['id']);

        $refunds = $this->stripe->refunds()->all($charge1['id']);

        $this->assertNotEmpty($refunds['data']);
        $this->assertCount(1, $refunds['data']);
        $this->assertIsArray($refunds['data']);
    }

    /** @test */
    public function it_can_retrieve_all_refunds_of_a_payment_intent()
    {
        $customer = $this->createCustomer();

        $this->createCardThroughToken($customer['id']);

        $payload = [
            'amount' => 3000,
            'currency' => 'USD',
            'confirm' => true,
            'customer' => $customer['id'],
        ];

        $paymentIntent1 = $this->stripe->paymentIntents()->create($payload);
        $paymentIntent2 = $this->stripe->paymentIntents()->create($payload);

        $this->stripe->refunds()->create($paymentIntent1['id']);

        $refunds1 = $this->stripe->refunds()->all($paymentIntent1['id']);
        $refunds2 = $this->stripe->refunds()->all($paymentIntent2['id']);

        $this->assertNotEmpty($refunds1['data']);
        $this->assertCount(1, $refunds1['data']);
        $this->assertIsArray($refunds1['data']);

        $this->assertEmpty($refunds2['data']);
        $this->assertCount(0, $refunds2['data']);
        $this->assertIsArray($refunds2['data']);
    }

    /** @test */
    public function it_can_retrieve_all_refunds_without_passing_the_charge_id()
    {
        $customer = $this->createCustomer();

        $charge1 = $this->createCharge($customer['id']);
        $charge2 = $this->createCharge($customer['id']);

        $this->stripe->refunds()->create($charge1['id']);
        $this->stripe->refunds()->create($charge2['id']);

        $refunds = $this->stripe->refunds()->all();

        $this->assertNotEmpty($refunds['data']);
        $this->assertIsArray($refunds['data']);
    }
}
