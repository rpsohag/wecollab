<?php

namespace Modules\Assistenza\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterAssistenzaSidebar implements \Maatwebsite\Sidebar\SidebarExtender
{
    /**
     * @var Authentication
     */
    protected $auth;

    /**
     * @param Authentication $auth
     *
     * @internal param Guard $guard
     */
    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    public function handle(BuildingSidebar $sidebar)
    {
        $sidebar->add($this->extendWith($sidebar->getMenu()));
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('core::sidebar.content'), function (Group $group) {
            $group->item('Assistenza', function (Item $item) {
                $item->icon('fa fa-question-circle');
                $item->weight(10);
                $item->authorize(
                     /* append */
                );
                $item->item(trans('assistenza::ticketinterventi.title.ticketinterventi'), function (Item $item) {
                    $item->icon('fa fa-ticket');
                    $item->weight(0);
                //    $item->append('admin.assistenza.ticketintervento.create');
                    $item->route('admin.assistenza.ticketintervento.index');
                    $item->authorize(
                        $this->auth->hasAccess('assistenza.ticketinterventi.index')
                    );
                });
                $item->item(trans('assistenza::richiesteinterventi.title.richiesteinterventi'), function (Item $item) {
                    $item->icon('fa fa-phone');
                    $item->weight(0);
                //    $item->append('admin.assistenza.richiesteintervento.create');
                    $item->route('admin.assistenza.richiesteintervento.index');
                    $item->authorize(
                        $this->auth->hasAccess('assistenza.richiesteinterventi.index')
                    );
                });
                $item->item('Ambienti', function (Item $item) {
                    $item->icon('fa fa-database');
                    $item->weight(0);
                    $item->route('admin.assistenza.ambienti.index');
                    $item->authorize(
                        $this->auth->hasAccess('assistenza.ambienti.index')
                    );
                });
                $item->item('Ambienti Di Conversione', function (Item $item) {
                    $item->icon('fa fa-database');
                    $item->weight(0);
                    $item->route('admin.assistenza.ambienticonversioni.index');
                    $item->authorize(
                        $this->auth->hasAccess('assistenza.ambienticonversioni.index')
                    );
                });
// append


            });
        });

        return $menu;
    }
}
