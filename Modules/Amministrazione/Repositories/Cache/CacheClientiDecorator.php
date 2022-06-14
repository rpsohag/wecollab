<?php

namespace Modules\Amministrazione\Repositories\Cache;

use Modules\Amministrazione\Repositories\ClientiRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheClientiDecorator extends BaseCacheDecorator implements ClientiRepository
{
    public function __construct(ClientiRepository $clienti)
    {
        parent::__construct();
        $this->entityName = 'amministrazione.clienti';
        $this->repository = $clienti;
    }
}
