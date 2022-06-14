<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\OffertaRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheOffertaDecorator extends BaseCacheDecorator implements OffertaRepository
{
    public function __construct(OffertaRepository $offerta)
    {
        parent::__construct();
        $this->entityName = 'commerciale.offertas';
        $this->repository = $offerta;
    }
}
