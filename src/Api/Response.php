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

use Guzzle\Service\Command\OperationCommand;
use Guzzle\Service\Command\ResponseClassInterface;
use Illuminate\Support\Collection;

class Response extends Collection implements ResponseClassInterface {

	/**
	 * Create a response model object from a completed command.
	 *
	 * @param  \Guzzle\Service\Command\OperationCommand  $command
	 * @return \Illuminate\Support\Collection
	 */
	public static function fromCommand(OperationCommand $command)
	{
		return new self($command->getResponse()->json());
	}

	/**
	 * Returns the given key value from the collection.
	 *
	 * @param  mixed  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->get($key, null);
	}

}
