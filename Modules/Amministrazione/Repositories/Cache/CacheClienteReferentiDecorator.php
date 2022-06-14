<?php

namespace Modules\Amministrazione\Repositories\Cache;

use Modules\Amministrazione\Repositories\ClienteReferentiRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheClienteReferentiDecorator extends BaseCacheDecorator implements ClienteReferentiRepository
{
    public function __construct(ClienteReferentiRepository $clientereferenti)
    {
        parent::__construct();
        $this->entityName = 'amministrazione.clientereferenti';
        $this->repository = $clientereferenti;
    }
}
