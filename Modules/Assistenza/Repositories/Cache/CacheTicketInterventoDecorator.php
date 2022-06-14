<?php

namespace Modules\Assistenza\Repositories\Cache;

use Modules\Assistenza\Repositories\TicketInterventoRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheTicketInterventoDecorator extends BaseCacheDecorator implements TicketInterventoRepository
{
    public function __construct(TicketInterventoRepository $ticketintervento)
    {
        parent::__construct();
        $this->entityName = 'assistenza.ticketinterventi';
        $this->repository = $ticketintervento;
    }
}
