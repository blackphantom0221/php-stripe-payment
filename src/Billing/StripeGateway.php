<?php namespace Cartalyst\Stripe\Billing;
/**
 * Part of the Stripe package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Stripe
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Carbon\Carbon;
use Cartalyst\Stripe\Api\Exception\NotFoundException;

abstract class StripeGateway {

	/**
	 * The billable entity
	 *
	 * @var \Cartalyst\Stripe\Billing\BillableInterface
	 */
	protected $billable;

	/**
	 * The Stripe client.
	 *
	 * @var \Cartalyst\Stripe\Api\Stripe
	 */
	protected $client;

	/**
	 * Constructor.
	 *
	 * @param  \Cartalyst\Stripe\Billing\BillableInterface  $billable
	 * @return void
	 */
	public function __construct(BillableInterface $billable)
	{
		$this->billable = $billable;

		$this->client = $this->getStripeClient();
	}

	/**
	 * Finds or creates a Stripe customer.
	 *
	 * @param  int  $id
	 * @param  array  $attributes
	 * @return array
	 */
	protected function findOrCreate($id, array $attributes = [])
	{
		try
		{
			$customer = $this->client->customers()->find(compact('id'));
		}
		catch (NotFoundException $e)
		{
			$customer = $this->client->customers()->create($attributes);

			$this->billable->stripe_id = $customer['id'];
			$this->billable->save();
		}

		return $customer;
	}

	/**
	 * Returns a Carbon object if the provided timestamp
	 * is valid and returns null otherwise.
	 *
	 * @param  int  $timestamp
	 * @return \Carbon\Carbon|null
	 */
	protected function nullableTimestamp($timestamp)
	{
		if ( ! $timestamp) return null;

		return Carbon::createFromTimestamp($timestamp);
	}

	/**
	 * Returns the Stripe client.
	 *
	 * @return \Cartalyst\Stripe\Api\Stripe
	 */
	protected function getStripeClient()
	{
		return $this->client ?: $this->billable->getStripeClient();
	}

	/**
	 * Converts the amount from "dollars" to cents.
	 *
	 * @param  int  $amount
	 * @return int
	 */
	protected function convertToCents($amount)
	{
		return (int) ($amount * 100);
	}

}
