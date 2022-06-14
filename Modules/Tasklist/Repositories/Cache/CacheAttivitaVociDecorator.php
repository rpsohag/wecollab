<?php

namespace Modules\Tasklist\Repositories\Cache;

use Modules\Tasklist\Repositories\AttivitaVociRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheAttivitaVociDecorator extends BaseCacheDecorator implements AttivitaVociRepository
{
    public function __construct(AttivitaVociRepository $attivitavoci)
    {
        parent::__construct();
        $this->entityName = 'tasklist.attivitavoci';
        $this->repository = $attivitavoci;
    }
}
