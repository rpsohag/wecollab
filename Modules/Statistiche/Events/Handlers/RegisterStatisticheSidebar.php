<?php

namespace Modules\Statistiche\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterstatisticheSidebar implements \Maatwebsite\Sidebar\SidebarExtender
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
            $group->item('Statistiche', function (Item $item) {
                $item->icon('fa fa-pie-chart');
                $item->weight(10);
                $item->authorize(
                     $this->auth->hasAccess('statistiche.statistica.index')
                );
                $item->item('Fatturazione', function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->route('admin.statistiche.statistica.fatturazione');
                    $item->authorize(
                        $this->auth->hasAccess('statistiche.statistica.fatturazione')
                    );
                });
                $item->item('Richieste intervento', function (Item $item) {
                    $item->icon('fa fa-phone');
                    $item->weight(0);
                    $item->route('admin.statistiche.statistica.richiesteintervento');
                    $item->authorize(
                        $this->auth->hasAccess('statistiche.statistica.richiesteintervento')
                    );
                }); 
                $item->item('Quadratura Timesheets', function (Item $item) {
                    $item->icon('fa fa-calendar-times-o');
                    $item->weight(0);
                    $item->route('admin.statistiche.quadraturatimesheets');
                    $item->authorize(
                        $this->auth->hasAccess('statistiche.quadraturatimesheets.index')
                    );
                });
                $item->item('Reports', function (Item $item) {
                    $item->icon('fa fa-bar-chart');
                    $item->weight(0);
                    $item->route('admin.statistiche.reports.index');
                    $item->authorize(
                        $this->auth->hasAccess('statistiche.reports.index')
                    );
                });
// append

            });
        });

        return $menu;
    }
}
