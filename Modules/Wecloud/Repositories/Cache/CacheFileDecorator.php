<?php

namespace Modules\Wecloud\Repositories\Cache;

use Modules\Wecloud\Repositories\FileRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheFileDecorator extends BaseCacheDecorator implements FileRepository
{
    public function __construct(FileRepository $file)
    {
        parent::__construct();
        $this->entityName = 'wecloud.file';
        $this->repository = $file;
    }
}
