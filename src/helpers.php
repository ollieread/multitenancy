<?php

/**
 * Helper method to avoid facades.
 *
 * @return \Ollieread\Multitenancy\TenantManager
 */
function multitenancy()
{
    return app('multitenancy');
}
