<?php
namespace Ollieread\Multitenancy\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel(): Model
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

    /**
     * @param string $identifier
     *
     * @return Tenant
     */
    public function retrieveBySubdomainIdentifier(string $identifier): ?Tenant
    {
        $model = $this->createModel();

        return $model
            ->newQuery()
            ->where($model->getSubdomainIdentifierName(), '=', $identifier)
            ->first();
    }

    /**
     * @param string $identifier
     *
     * @return mixed
     */
    public function retrieveByDomainIdentifier(string $identifier): ?Tenant
    {
        $model = $this->createModel();

        return $model
            ->newQuery()
            ->where($model->getDomainIdentifierName(), '=', $identifier)
            ->first();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function retrieveAll(): Collection
    {
        return $this->createModel()
            ->newQuery()
            ->get();
    }
}