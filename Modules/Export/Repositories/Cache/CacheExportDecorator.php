<?php

namespace Modules\Export\Repositories\Cache;

use Modules\Export\Repositories\ExportRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheExportDecorator extends BaseCacheDecorator implements ExportRepository
{
    public function __construct(ExportRepository $export)
    {
        parent::__construct();
        $this->entityName = 'export.exports';
        $this->repository = $export;
    }
}
