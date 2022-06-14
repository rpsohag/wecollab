<?php

namespace Modules\Wecore\Repositories\Cache;

use Modules\Wecore\Repositories\CoreRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheCoreDecorator extends BaseCacheDecorator implements CoreRepository
{
    public function __construct(CoreRepository $core)
    {
        parent::__construct();
        $this->entityName = 'wecore.cores';
        $this->repository = $core;
    }
}
