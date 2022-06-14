<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\AnalisiVenditaRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheAnalisiVenditaDecorator extends BaseCacheDecorator implements AnalisiVenditaRepository
{
    public function __construct(AnalisiVenditaRepository $analisivendita)
    {
        parent::__construct();
        $this->entityName = 'commerciale.analisivenditas';
        $this->repository = $analisivendita;
    }
}
