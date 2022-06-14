<?php

namespace Modules\Commerciale\Repositories\Cache;

use Modules\Commerciale\Repositories\SegnalazioneOpportunitaRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheSegnalazioneOpportunitaDecorator extends BaseCacheDecorator implements SegnalazioneOpportunitaRepository
{
    public function __construct(SegnalazioneOpportunitaRepository $segnalazioneopportunita)
    {
        parent::__construct();
        $this->entityName = 'commerciale.segnalazioniopportunita';
        $this->repository = $segnalazioneopportunita;
    }
}
