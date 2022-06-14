<?php

namespace Modules\Statistiche\Repositories\Cache;

use Modules\Statistiche\Repositories\StatisticaRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheStatisticaDecorator extends BaseCacheDecorator implements StatisticaRepository
{
    public function __construct(StatisticaRepository $statistica)
    {
        parent::__construct();
        $this->entityName = 'statistiche.statisticas';
        $this->repository = $statistica;
    }
}
