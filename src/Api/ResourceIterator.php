<?php namespace Cartalyst\Stripe\Api;
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

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Resource\ResourceIterator as BaseResourceIterator;

class ResourceIterator extends BaseResourceIterator {

	/**
	 * {@inheritDoc}
	 */
	public function __construct(CommandInterface $command, array $data = [])
	{
		parent::__construct($command, $data);

		$this->pageSize = 100;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function sendRequest()
	{
		$this->command->set('limit', $this->pageSize);

		if ($this->nextToken)
		{
			$this->command->set('starting_after', $this->nextToken);
		}

		$result = $this->command->execute();

		$data = $result['data'];

		$lastItem = end($data);

		$this->nextToken = $result['has_more'] ? $lastItem['id'] : false;

		return $data;
	}

}
