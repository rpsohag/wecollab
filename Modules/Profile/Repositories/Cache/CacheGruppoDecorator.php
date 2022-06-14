<?php

namespace Modules\Profile\Repositories\Cache;

use Modules\Profile\Repositories\GruppoRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheGruppoDecorator extends BaseCacheDecorator implements GruppoRepository
{
    public function __construct(GruppoRepository $gruppo)
    {
        parent::__construct();
        $this->entityName = 'profile.gruppos';
        $this->repository = $gruppo;
    }
}
