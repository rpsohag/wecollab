<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\FatturazioneRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheFatturazioneDecorator extends BaseCacheDecorator implements FatturazioneRepository
{
    public function __construct(FatturazioneRepository $fatturazione)
    {
        parent::__construct();
        $this->entityName = 'commerciale.fatturazioni';
        $this->repository = $fatturazione;
    }
}
