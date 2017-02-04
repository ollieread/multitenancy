<?php
namespace Ollieread\Multitenancy\Providers;

use Illuminate\Database\Eloquent\Model;
use Ollieread\Multitenancy\Contracts\Provider;
use Ollieread\Multitenancy\Contracts\Tenant;
use Ollieread\Multitenancy\Contracts\TenantPrimary;
use Ollieread\Multitenancy\Contracts\TenantSecondary;

/**
 * Class Eloquent
 *
 * @package Ollieread\Multitenancy\Providers
 */
class Eloquent implements Provider
{

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param      $identifier
     * @param bool $primary
     *
     * @return Tenant
     */
    public function retrieveByIdentifier($identifier, $primary = true)
    {
        $model = $this->createModel();
        $query = $model->newQuery();

        $query->where($primary ? $model->getPrimaryIdentifierName() : $model->getSecondaryIdentifierName(), '=', $identifier);

        return $query->first();
    }

    /**
     * @return Tenant
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets the name of the Eloquent user model.
     *
     * @param  string  $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}