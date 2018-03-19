<?php

namespace Ollieread\Multitenancy\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

/**
 * Class ManagesConnection
 *
 * @package Ollieread\Multitenancy\Concerns
 */
trait ManagesConnection
{

    /**
     * @var callable
     */
    protected $defaultConnectionResolver;

    /**
     * @var array<callable>
     */
    protected $connectionResolvers = [];

    /**
     * @var array<string>
     */
    protected $connections = [];

    public function setConnections(array $connections)
    {
        $this->connections = $connections;

        return $this;
    }

    /**
     * Set the resolver for the connection.
     *
     * @param callable    $resolver
     * @param null|string $connection
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function setConnectionResolver(callable $resolver, ?string $connection = null)
    {
        if (! $connection) {
            $this->defaultConnectionResolver = $resolver;
        } else {
            if (\in_array($connection, $this->connections, true)) {
                $this->connectionResolvers[$connection] = $resolver;
            } else {
                throw new \RuntimeException('Invalid connection provided');
            }
        }

        return $this;
    }

    /**
     * Parse the connection configuration.
     *
     * @param array       $config
     * @param null|string $connection
     *
     * @return mixed
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function resolveConnection(array $config, ?string $connection = null)
    {
        if ($connection && ! \in_array($connection, $this->connections, true)) {
            throw new \RuntimeException('Invalid connection provided');
        }

        $resolver = (! $connection || ! isset($this->connectionResolvers[$connection])) ? $this->defaultConnectionResolver : $this->connectionResolvers[$connection];

        if (\is_callable($resolver)) {
            return $resolver($config, $this->tenant, $connection);
        }

        throw new \InvalidArgumentException('No connection parser set');
    }

    /**
     * @param string $connection
     *
     * @return \Illuminate\Database\Connection
     * @throws \RuntimeException
     */
    public function connection(string $connection): Connection
    {
        if ($connection && ! \in_array($connection, $this->connections, true)) {
            throw new \RuntimeException('Invalid connection provided');
        }

        return app(DatabaseManager::class)->connection($connection);
    }

    /**
     *
     */
    public function reconnect()
    {
        array_walk($this->connections, function ($connection) {
            $this->connection($connection)->reconnect();
        });
    }
}