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
use Cartalyst\Stripe\Billing\BillableInterface;
use Cartalyst\Stripe\Billing\Models\IlluminateCharge;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChargeGateway extends StripeGateway {

	/**
	 * The Eloquent charge object.
	 *
	 * @var \Cartalyst\Stripe\Billing\Models\IlluminateCharge
	 */
	protected $charge;

	/**
	 * Flag to wether capture the charge or not.
	 *
	 * @var bool
	 */
	protected $capture = true;

	/**
	 * Indicates the charge currency.
	 *
	 * @var stirng
	 */
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
	 * @param  \Cartalyst\Stripe\Billing\BillableInterface  $billable
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
	 * @return \Cartalyst\Stripe\Api\Response
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

		// Prepare the payload
		$payload = array_merge($attributes, [
			'customer' => $entity->stripe_id,
			'capture'  => $this->capture,
			'currency' => $this->currency,
			'amount'   => $amount,
			'card'     => $card,
		]);

		// Create the charge on Stripe
		$charge = $this->client->charges()->create($payload);

		// Attach the created charge to the billable entity
		$this->storeCharge($charge);

		return $charge;
	}

	/**
	 * Updates the charge.
	 *
	 * @param  array  $attributes
	 * @return \Cartalyst\Stripe\Api\Response
	 */
	public function update(array $attributes = [])
	{
		// Prepare the payload
		$payload = $this->getPayload($attributes);

		// Update the charge on Stripe
		$charge = $this->client->charges()->update($payload);

		// Update the charge on storage
		$this->storeCharge($charge);

		return $charge;
	}

	/**
	 * Refunds the charge.
	 *
	 * @param  int  $amount
	 * @return \Cartalyst\Stripe\Api\Response
	 */
	public function refund($amount = null)
	{
		// Prepare the payload
		$payload = $this->getPayload(array_filter(compact('amount')));

		// Refunds the charge on Stripe
		$refund = $this->client->charges()->refund($payload);

		// Create the local refund entry
		$this->storeChargeRefund($this->charge, $refund);

		// Get the updated charge
		$charge = $this->client->charges()->find($this->getPayload());

		// Update the charge on storage
		$this->storeCharge($charge);

		return $charge;
	}

	/**
	 * Captures the charge.
	 *
	 * @return \Cartalyst\Stripe\Api\Response
	 */
	public function capture()
	{
		// Prepare the payload
		$payload = $this->getPayload();

		// Capture the charge on Stripe
		$charge = $this->client->charges()->capture($payload);

		// Fire the 'cartalyst.stripe.charge.captured' event
		$this->fire('charge.captured', [ $charge, $model ]);

		// Disable the event dispatcher
		$this->disableEventDispatcher();

		// Update the charge on storage
		$model = $this->storeCharge($charge);

		// Enable the event dispatcher
		$this->enableEventDispatcher();

		return $charge;
	}

	/**
	 * Disables the charge from being captured.
	 *
	 * @return \Cartalyst\Stripe\Billing\ChargeGateway
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
	 * @return \Cartalyst\Stripe\Billing\ChargeGateway
	 */
	public function setCurrency($currency)
	{
		$this->currency = $currency;

		return $this;
	}

	/**
	 * Sets the token that'll be used to create a new credit card.
	 *
	 * @param  string  $token
	 * @return \Cartalyst\Stripe\Billing\ChargeGateway
	 */
	public function setToken($token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	 * Syncronizes the Stripe charges data with the local data.
	 *
	 * @return void
	 * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 */
	public function syncWithStripe()
	{
		// Get the entity object
		$entity = $this->billable;

		// Check if the entity is a stripe customer
		if ( ! $entity->isBillable())
		{
			throw new BadRequestHttpException("The entity isn't a Stripe Customer!");
		}

		// Get all the entity charges
		$charges = array_reverse($this->client->chargesIterator([
			'customer' => $entity->stripe_id,
		])->toArray());

		// Loop through the charges
		foreach ($charges as $charge)
		{
			$this->storeCharge($charge);
		}
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

	/**
	 * Stores the charge information on local storage.
	 *
	 * @param  \Cartalyst\Stripe\Api\Response  $charge
	 * @return \Cartalyst\Stripe\Billing\Models\IlluminateCharge
	 */
	protected function storeCharge($charge)
	{
		// Get the entity object
		$entity = $this->billable;

		// Get the charge id
		$stripeId = $charge['id'];

		// Find the charge on storage
		$_charge = $entity->charges()->where('stripe_id', $stripeId)->first();

		// Flag to know which event needs to be fired
		$event = ! $_charge ? 'created' : 'updated';

		// Prepare the payload
		$payload = [
			'stripe_id'   => $stripeId,
			'invoice_id'  => $charge['invoice'],
			'currency'    => $charge['currency'],
			'description' => $charge['description'],
			'amount'      => $this->convertToDecimal($charge['amount']),
			'paid'        => (bool) $charge['paid'],
			'captured'    => (bool) $charge['captured'],
			'refunded'    => (bool) $charge['refunded'],
			'created_at'  => Carbon::createFromTimestamp($charge['created']),
		];

		// Does the charge exist on storage?
		if ( ! $_charge)
		{
			$_charge = $entity->charges()->create($payload);
		}
		else
		{
			$_charge->update($payload);
		}

		// Fires the appropriate event
		$this->fire("charge.{$event}", [ $charge, $_charge ]);

		// Get all the refunds of this charge
		$refunds = $this->client->refundsIterator([
			'charge' => $stripeId,
		]);

		// Loop through the refunds
		foreach ($refunds as $refund)
		{
			$this->storeChargeRefund($_charge, $refund);
		}

		return $_charge;
	}

	/**
	 * Stores the charge refund information on local storage.
	 *
	 * @param  \Cartalyst\Stripe\Billing\Models\IlluminateCharge
	 * @param  \Cartalyst\Stripe\Api\Response  $refund
	 * @return \Cartalyst\Stripe\Billing\Models\IlluminateRefund
	 */
	protected function storeChargeRefund($charge, $refund)
	{
		// Get the transaction id
		$transactionId = $refund['balance_transaction'];

		// Find the refund on storage
		$_refund = $charge->refunds()->where('transaction_id', $transactionId)->first();

		// Flag to know which event needs to be fired
		$event = ! $_refund ? 'created' : 'updated';

		// Prepare the payload
		$payload = [
			'transaction_id' => $transactionId,
			'amount'         => $this->convertToDecimal($refund['amount']),
			'currency'       => $refund['currency'],
			'created_at'     => Carbon::createFromTimestamp($refund['created']),
		];

		// Does the refund exists on storage?
		if ( ! $_refund)
		{
			$_refund = $charge->refunds()->create($payload);
		}
		else
		{
			$_refund->update($payload);
		}

		// Fires the appropriate event
		$this->fire("charge.refund.{$event}", [ $refund, $_refund ]);

		return $_refund;
	}

}
