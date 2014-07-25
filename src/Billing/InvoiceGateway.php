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
use Cartalyst\Stripe\Billing\Models\IlluminateInvoice;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvoiceGateway extends StripeGateway {

	/**
	 * The invoice object.
	 *
	 * @var \Cartalyst\Stripe\Billing\Models\IlluminateInvoice
	 */
	protected $invoice;

	/**
	 * Constructor.
	 *
	 * @param  \Cartalyst\Stripe\Billing\BillableInterface  $billable
	 * @param  mixed  $invoice
	 * @return void
	 */
	public function __construct(BillableInterface $billable, $invoice = null)
	{
		parent::__construct($billable);

		if (is_numeric($invoice))
		{
			$invoice = $this->billable->invoices->find($invoice);
		}

		if ($invoice instanceof IlluminateInvoice)
		{
			$this->invoice = $invoice;
		}
	}

	/**
	 * Syncronizes the Stripe invoices data with the local data.
	 *
	 * @return void
	 * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 */
	public function syncWithStripe()
	{
		$entity = $this->billable;

		if ( ! $entity->isBillable())
		{
			throw new BadRequestHttpException("The entity isn't a Stripe Customer!");
		}

		$invoices = array_reverse($this->client->invoicesIterator([
			'customer' => $entity->stripe_id,
		])->toArray());

		foreach ($invoices as $invoice)
		{
			$stripeId = $invoice['id'];

			$_invoice = $entity->invoices()->where('stripe_id', $stripeId)->first();

			$data = [
				'stripe_id'       => $stripeId,
				'subscription_id' => $invoice['subscription'],
				'currency'        => $invoice['currency'],
				'subtotal'        => $this->convertToDecimal($invoice['subtotal']),
				'total'           => $this->convertToDecimal($invoice['total']),
				'amount_due'      => $this->convertToDecimal($invoice['amount_due']),
				'attempted'       => (bool) $invoice['attempted'],
				'closed'          => (bool) $invoice['closed'],
				'paid'            => (bool) $invoice['paid'],
				'attempt_count'   => $invoice['attempt_count'],
				'created_at'      => Carbon::createFromTimestamp($invoice['date']),
				'period_start'    => $this->nullableTimestamp($invoice['period_start']),
				'period_end'      => $this->nullableTimestamp($invoice['period_end']),
			];

			if ( ! $_invoice)
			{
				$_invoice = $entity->invoices()->create($data);
			}
			else
			{
				$_invoice->update($data);
			}

			foreach ($invoice['lines']['data'] as $item)
			{
				$stripeId = $item['id'];

				$_item = $_invoice->items()->where('stripe_id', $stripeId)->first();

				$type = array_get($item, 'type', null);

				$data = [
					'stripe_id'    => $stripeId,
					'currency'     => $item['currency'],
					'type'         => $type,
					'amount'       => $this->convertToDecimal($item['amount']),
					'proration'    => (bool) $item['proration'],
					'description'  => $this->prepareInvoiceItemDescription($type, $item),
					'plan_id'      => array_get($item, 'plan.id', null),
					'quantity'     => array_get($item, 'quantity', null),
					'period_start' => $this->nullableTimestamp(array_get($item, 'period.start', null)),
					'period_end'   => $this->nullableTimestamp(array_get($item, 'period.end', null)),
				];

				if ( ! $_item)
				{
					$_item = $_invoice->items()->create($data);
				}
				else
				{
					$_item->update($data);
				}
			}
		}
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
