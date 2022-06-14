<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\CensimentoClienteRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCensimentoClienteDecorator extends BaseCacheDecorator implements CensimentoClienteRepository
{
    public function __construct(CensimentoClienteRepository $censimentocliente)
    {
        parent::__construct();
        $this->entityName = 'commerciale.censimenticlienti';
        $this->repository = $censimentocliente;
    }
}
