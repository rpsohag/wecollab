<?php

namespace Modules\Profile\Repositories\Cache;

use Modules\Profile\Repositories\FiguraProfessionaleRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheFiguraProfessionaleDecorator extends BaseCacheDecorator implements FiguraProfessionaleRepository
{
    public function __construct(FiguraProfessionaleRepository $figuraprofessionale)
    {
        parent::__construct();
        $this->entityName = 'profile.figureprofessionali';
        $this->repository = $figuraprofessionale;
    }
}
