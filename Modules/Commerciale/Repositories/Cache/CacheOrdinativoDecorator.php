<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\OrdinativoRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheOrdinativoDecorator extends BaseCacheDecorator implements OrdinativoRepository
{
    public function __construct(OrdinativoRepository $ordinativo)
    {
        parent::__construct();
        $this->entityName = 'commerciale.ordinativi';
        $this->repository = $ordinativo;
    }
}
