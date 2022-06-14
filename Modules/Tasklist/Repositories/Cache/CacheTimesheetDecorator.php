<?php

namespace Modules\Tasklist\Repositories\Cache;

use Modules\Tasklist\Repositories\TimesheetRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheTimesheetDecorator extends BaseCacheDecorator implements TimesheetRepository
{
    public function __construct(TimesheetRepository $timesheet)
    {
        parent::__construct();
        $this->entityName = 'tasklist.timesheets';
        $this->repository = $timesheet;
    }
}
