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

class InvoiceItemsGateway extends StripeGateway {

	/**
	 * Creates a new invoice item on the entity.
	 *
	 * @param  array  $attributes
	 * @return \Cartalyst\Stripe\Billing\Models\IlluminateInvoiceItem
	 */
	public function create(array $attributes = [])
	{
		// Get the entity object
		$entity = $this->billable;

		// Find or Create the Stripe customer that
		// will belong to this billable entity.
		$customer = $this->findOrCreate(
			$entity->stripe_id,
			array_get($attributes, 'customer', [])
		);

		// Prepare the payload
		$attributes = array_merge($attributes, [
			'customer' => $entity->stripe_id,
		]);

		// Create the invoice item on Stripe
		$response = $this->client->invoiceItems()->create($attributes);

		// Store the item on storage
		$item = $this->storeItem($response);

		return $item;
	}

	/**
	 * Updates the given invoice item on the entity.
	 *
	 * @param  string  $id
	 * @param  array  $attributes
	 * @return \Cartalyst\Stripe\Billing\Models\IlluminateInvoiceItem
	 */
	public function update($id, array $attributes = [])
	{
		// Prepare the payload
		$payload = array_merge($attributes, compact('id'));

		// Delete the invoice item on Stripe
		$response = $this->client->invoiceItems()->update($payload);

		// Store the item on storage
		$item = $this->storeItem($response);

		return $item;
	}

	/**
	 * Deletes the given invoice item on the entity.
	 *
	 * @param  string  $id
	 * @return \Cartalyst\Stripe\Api\Response
	 */
	public function delete($id)
	{
		// Delete the invoice item on Stripe
		$response = $this->client->invoiceItems()->destroy(compact('id'));

		// Fire the 'cartalyst.stripe.invoice.item.deleted' event
		$this->fire('invoice.item.deleted', [ $response ]);

		return $response;
	}

	/**
	 * Stores the invoice item information on local storage.
	 *
	 * @param  \Cartalyst\Stripe\Api\Response|array  $response
	 * @param  \Cartalyst\Stripe\Billing\Models\IlluminateInvoice  $invoice
	 * @return \Cartalyst\Stripe\Billing\Models\IlluminateInvoiceItem
	 */
	public function storeItem($response, $invoice = null)
	{
		// Get the entity object
		$entity = $this->billable;

		// Get the invoice item id
		$stripeId = $response['id'];

		// Get the invoice item type
		$type = array_get($response, 'type', 'invoiceitem');

		// Find the invoice item on storage
		$item = $entity->invoiceItems()
			->where('stripe_id', $stripeId)
			->where('type', $type)
			->first();

		// Flag to know which event needs to be fired
		$event = ! $item ? 'created' : 'updated';

		// Prepare the payload
		$payload = [
			'stripe_id'    => $stripeId,
			'invoice_id'   => $invoice ? $invoice->id : 0,
			'currency'     => $response['currency'],
			'type'         => $type,
			'amount'       => $this->convertToDecimal($response['amount']),
			'proration'    => (bool) $response['proration'],
			'description'  => $this->prepareInvoiceItemDescription($type, $response),
			'plan_id'      => array_get($response, 'plan.id', null),
			'quantity'     => array_get($response, 'quantity', null),
			'period_start' => $this->nullableTimestamp(array_get($response, 'period.start', null)),
			'period_end'   => $this->nullableTimestamp(array_get($response, 'period.end', null)),
		];

		// Does the invoice item exist on storage?
		if ( ! $item)
		{
			$item = $entity->invoiceItems()->create($payload);
		}
		else
		{
			$item->update($payload);
		}

		// Fire the 'cartalyst.stripe.invoice.item.created' event
		$this->fire("invoice.item.{$event}", [ $response, $item ]);

		return $item;
	}

	/**
	 * Prepares the invoice item description.
	 *
	 * @param  string  $type
	 * @param  array  $item
	 * @return string
	 */
	protected function prepareInvoiceItemDescription($type, $item)
	{
		return $type === 'subscription' ? $item['plan']['name'] : $item['description'];
	}

}
