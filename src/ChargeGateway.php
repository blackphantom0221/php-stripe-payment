<?php namespace Cartalyst\Stripe;
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
use Cartalyst\Stripe\BillableInterface;
use Cartalyst\Stripe\Models\IlluminateCharge;

class ChargeGateway extends StripeGateway {

	/**
	 * The charge object.
	 *
	 * @var \Cartalyst\Stripe\Models\IlluminateCharge
	 */
	protected $charge;

	protected $capture = true;

	protected $currency = 'usd';

	/**
	 * The token for the new credit card.
	 *
	 * @var string
	 */
	protected $token;

	/**
	 * Constructor.
	 *
	 * @param  \Cartalyst\Stripe\BillableInterface  $billable
	 * @param  mixed  $charge
	 * @return void
	 */
	public function __construct(BillableInterface $billable, $charge = null)
	{
		parent::__construct($billable);

		if (is_numeric($charge))
		{
			$charge = $this->billable->charges->find($charge);
		}

		if ($charge instanceof IlluminateCharge)
		{
			$this->charge = $charge;
		}
	}

	/**
	 * Creates a new charge on the entity.
	 *
	 * @param  int  $amount
	 * @param  array  $attributes
	 * @return array
	 */
	public function create($amount, array $attributes = [])
	{
		// Get the entity object
		$entity = $this->billable;

		// Find or Create the Stripe customer that
		// will belong to this billable entity.
		$customer = $this->findOrCreate(
			$entity->stripe_id,
			array_get($attributes, 'customer', [])
		);

		// Get the current default card identifier
		$card = $customer['default_card'];

		// If a stripe token is provided, we'll use it and
		// attach the credit card to the Stripe customer.
		if ($this->token)
		{
			$card = $entity->card()->makeDefault()->create(
				$this->token,
				array_get($attributes, 'card', [])
			);

			$card = $card['id'];
		}

		// Prepare the charge data
		$attributes = array_merge($attributes, [
			'customer' => $entity->stripe_id,
			'capture'  => $this->capture ? 'true' : 'false',
			'currency' => $this->currency,
			'amount'   => $this->convertToCents($amount),
			'card'     => $card,
		]);

		// Create the charge on Stripe
		$charge = $this->client->charges()->create($attributes)->toArray();

		// Attach the created charge to the billable entity
		$this->billable->charges()->create([
			'stripe_id'   => $charge['id'],
			'amount'      => $amount,
			'paid'        => $charge['paid'],
			'captured'    => $charge['captured'],
			'refunded'    => $charge['refunded'],
			'description' => array_get($attributes, 'description', null),
		]);

		return $charge;
	}

	/**
	 * Updates the charge.
	 *
	 * @param  array  $attributes
	 * @return array
	 */
	public function update(array $attributes = [])
	{
		$payload = $this->getPayload($attributes);

		return $this->client->charges()->update($payload)->toArray();
	}

	/**
	 * Refunds the charge.
	 *
	 * @param  int  $amount
	 * @return array
	 */
	public function refund($amount = null)
	{
		$payload = $this->getPayload(array_filter([
			'amount' => $this->convertToCents($amount),
		]));

		$charge = $this->client->charges()->refund($payload)->toArray();

		$refunded = $charge['refunded'];

		$this->charge->update([
			'refunded' => $refunded,
		]);

		if ($refunded)
		{
			$this
				->charge
				->refunds()
				->create([
					'transaction_id' => $charge['balance_transaction'],
					'amount'         => ($charge['amount_refunded'] / 100),
				]);
		}

		return $charge;
	}

	/**
	 * Captures the charge.
	 *
	 * @return array
	 */
	public function capture()
	{
		$payload = $this->getPayload();

		return $this->client->charges()->capture($payload)->toArray();
	}

	/**
	 * Disables the charge from being captured.
	 *
	 * @return \Cartalyst\Stripe\ChargeGateway
	 */
	public function disableCapture()
	{
		$this->capture = false;

		return $this;
	}

	/**
	 * Sets the currency to be used upon a new charge.
	 *
	 * @param  string  $currency
	 * @return \Cartalyst\Stripe\ChargeGateway
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;

		return $this;
	}

	/**
	 * Sets the token that'll be used to
	 * create a new credit card.
	 *
	 * @param  string  $token
	 * @return \Cartalyst\Stripe\ChargeGateway
	 */
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	 * Returns the request payload.
	 *
	 * @param  array  $attributes
	 * @return array
	 */
	protected function getPayload(array $attributes = [])
	{
		return array_merge($attributes, [
			'id' => $this->charge->stripe_id,
		]);
	}

}
