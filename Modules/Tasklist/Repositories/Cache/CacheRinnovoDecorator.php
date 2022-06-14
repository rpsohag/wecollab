<?php

namespace Modules\Tasklist\Repositories\Cache;

use Modules\Tasklist\Repositories\RinnovoRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheRinnovoDecorator extends BaseCacheDecorator implements RinnovoRepository
{
    public function __construct(RinnovoRepository $rinnovo)
    {
        parent::__construct();
        $this->entityName = 'tasklist.rinnovi';
        $this->repository = $rinnovo;
    }
}
