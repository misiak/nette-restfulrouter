<?php

namespace Misiak\Application\Routers;

/**
 * Restful route for Nette
 *
 * @author    Michal Kvasničák <michal@kvasnicak.info>
 *
 * @copyright Copyright (c) 2012, Michal Kvasničák
 * @license   MIT
 * @license   http://github.com/misiak/nette-restfulrouter/blob/master/license.md
 *
 * @package   Misiak\Application\Routers
 *
 */
class Route extends \Nette\Application\Routers\Route
{


    /**
     * Possible methods (affects matching)
     *
     * @var array
     */
    protected $methods = [];


    /**
     * Creates Route
     *
     * @param array|string $method  array of methods or methods delimited by |
     * @param array        $mask
     * @param array        $metadata
     * @param int          $flags
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($method, $mask, $metadata = array(), $flags = 0)
    {
        parent::__construct($mask, $metadata, $flags);

        if (empty($method))
        {
            throw new \InvalidArgumentException('Method has to be set.');
        }

        if ( is_string($method) )
        {
            $methods = explode('|', trim($method, '|'));
        }
        elseif ( is_array($method) )
        {
            $methods = $method;
        }
        else
        {
            throw new \InvalidArgumentException('Method has to be string or array.');
        }

        // set methods to match
        $this->methods = $methods;
    }


    /**
     * Tries to match request
     *
     * @param \Nette\Http\IRequest $httpRequest
     *
     * @return \Nette\Application\Request|NULL
     */
    public function match(\Nette\Http\IRequest $httpRequest)
    {
        foreach ($this->methods as $method)
        {
            // method is not matched, return null
            if ( $httpRequest->isMethod($method))
            {
                return parent::match($httpRequest);
            }
        }

        return null;
    }


    /**
     * {@inheritDoc}
     */
    public function constructUrl(\Nette\Application\Request $appRequest, \Nette\Http\Url $refUrl)
    {
        if ( ! $this->matchMethod($appRequest))
        {
            return null;
        }

        $parameters = $appRequest->parameters;
        // is matched, remove _method parameter !
        if (isset($parameters))
        {
            unset($parameters['_method']);
            $appRequest->setParameters($parameters);
        }

        return parent::constructUrl($appRequest, $refUrl);
    }


    /**
     * Tries to match method of app request
     *
     * @param \Nette\Application\Request $appRequest
     *
     * @return bool
     */
    private function matchMethod(\Nette\Application\Request $appRequest)
    {
        $methodToMatch = isset($appRequest->parameters['_method']) ? $appRequest->parameters['_method'] : $appRequest->method;

        foreach ($this->methods as $method)
        {
            if (strcasecmp($method, $methodToMatch) === 0)
            {
                return true;
            }
        }

        return false;
    }
}
