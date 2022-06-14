<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\SimAziendaliRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheSimAziendaliDecorator extends BaseCacheDecorator implements SimAziendaliRepository
{
    public function __construct(SimAziendaliRepository $simaziendali)
    {
        parent::__construct();
        $this->entityName = 'commerciale.simaziendalis';
        $this->repository = $simaziendali;
    }
}
