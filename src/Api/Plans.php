<?php

/**
 * Part of the Stripe package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Stripe
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Stripe\Api;

class Plans extends Api
{
    /**
     * Creates a new plan.
     *
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function create(array $parameters = [])
    {
        return $this->_post('v1/plans', $parameters);
    }

    /**
     * Retrieves an existing plan.
     *
     * @param  string  $planId
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function find($planId)
    {
        return $this->_get("v1/plans/{$planId}");
    }

    /**
     * Updates an existing plan.
     *
     * @param  string  $planId
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function update($planId, array $parameters = [])
    {
        return $this->_post("v1/plans/{$planId}", $parameters);
    }

    /**
     * Deletes an existing plan.
     *
     * @param  string  $planId
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function delete($planId)
    {
        return $this->_delete("v1/plans/{$planId}");
    }

    /**
     * Lists all plans.
     *
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function all(array $parameters = [])
    {
        return $this->_get('v1/plans', $parameters);
    }
}
