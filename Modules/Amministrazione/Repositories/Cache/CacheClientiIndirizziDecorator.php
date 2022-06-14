<?php

namespace Modules\Amministrazione\Repositories\Cache;

use Modules\Amministrazione\Repositories\clientiIndirizziRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheClientiIndirizziDecorator extends BaseCacheDecorator implements clientiIndirizziRepository
{
    public function __construct(clientiIndirizziRepository $clientiindirizzi)
    {
        parent::__construct();
        $this->entityName = 'amministrazione.clientiindirizzi';
        $this->repository = $clientiindirizzi;
    }
}
