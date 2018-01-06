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
    protected $connectionParser;

    /**
     * Set the parser for the connection configuration.
     *
     * @param callable $connection
     *
     * @return mixed
     */
    public function setConnectionParser(callable $connection)
    {
        $this->connectionParser = $connection;

        return $this;
    }

    /**
     * Parse the connection configuration.
     *
     * @param array $config
     *
     * @return mixed
     */
    public function parseConnection(array $config)
    {
        if (is_callable($this->connectionParser)) {
            return call_user_func($this->connectionParser, $config, $this->tenant);
        }

        throw new \InvalidArgumentException('No connection parser set');
    }

    /**
     * @return \Illuminate\Database\Connection
     */
    public function connection(): Connection
    {
        return app(DatabaseManager::class)->connection($this->config['multidatabase']['connection']);
    }

    /**
     *
     */
    public function reconnect()
    {
        $this->connection()->reconnect();
    }
}