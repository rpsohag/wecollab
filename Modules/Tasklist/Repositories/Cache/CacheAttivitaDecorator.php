<?php

namespace Modules\Tasklist\Repositories\Cache;

use Modules\Tasklist\Repositories\AttivitaRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheAttivitaDecorator extends BaseCacheDecorator implements AttivitaRepository
{
    public function __construct(AttivitaRepository $attivita)
    {
        parent::__construct();
        $this->entityName = 'tasklist.attivitas';
        $this->repository = $attivita;
    }
}
