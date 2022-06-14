<?php

namespace Modules\Assistenza\Repositories\Cache;

use Modules\Assistenza\Repositories\RichiesteInterventoRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheRichiesteInterventoDecorator extends BaseCacheDecorator implements RichiesteInterventoRepository
{
    public function __construct(RichiesteInterventoRepository $richiesteintervento)
    {
        parent::__construct();
        $this->entityName = 'assistenza.richiesteinterventi';
        $this->repository = $richiesteintervento;
    }
}
