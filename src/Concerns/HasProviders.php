<?php

namespace Ollieread\Multitenancy\Concerns;

use Ollieread\Multitenancy\Contracts\Provider;

/**
 * Interface HasProviders
 *
 * @package Ollieread\Multitenancy\Concerns
 */

trait HasProviders
{
    /**
     * Array of providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * Pass any method calls not found through to the provider.
     *
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->provider()->{$method}(...$parameters);
    }

    /**
     * Retrieve or create the current provider.
     *
     * @return \Ollieread\Multitenancy\Contracts\Provider
     */
    public function provider()
    {
        return $this->provider ? $this->provider : $this->provider = $this->resolve();
    }

    /**
     * Resolve to a provider.
     *
     * @return \Ollieread\Multitenancy\Contracts\Provider
     */
    public function resolve()
    {
        return $this->createProvider();
    }

    /**
     * Extend the manager with a new provider.
     *
     * @param          $name
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($name, \Closure $callback)
    {
        $this->providers[$name] = $callback;

        return $this;
    }

    /**
     * Create an instance of the provider.
     *
     * @return Provider
     */
    protected function createProvider()
    {
        if (isset($this->providers[$this->config['provider']])) {
            return call_user_func(
                $this->providers[$this->config['provider']],
                $this->app,
                $this->getProviderConfig($this->config['provider'])
            );
        }

        throw new \RuntimeException('Invalid provider: ' . $this->config['provider']);
    }

    /**
     * Retrieve the config for the specified provider.
     *
     * @param $provider
     *
     * @return array
     */
    protected function getProviderConfig($provider)
    {
        if (isset($this->config['providers'][$provider])) {
            return $this->config['providers'][$provider];
        }

        return [];
    }
}