<?php

namespace Modules\Tasklist\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterTasklistSidebar implements \Maatwebsite\Sidebar\SidebarExtender
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
            $group->item(trans('Tasklist'), function (Item $item) {
                $item->icon('fa fa-superpowers');
                $item->weight(10);
                $item->authorize(
                     /* append */
                );
                $item->item(trans('Attività'), function (Item $item) {
                    $item->icon('fa fa-tasks');
                    $item->weight(0);
                 //   $item->append('admin.tasklist.attivita.create');
                    $item->route('admin.tasklist.attivita.index');
                    $item->authorize(
                        $this->auth->hasAccess('tasklist.attivita.index')
                    );
                });
                $item->item(trans('tasklist::timesheets.title.timesheets'), function (Item $item) {
                    $item->icon('fa fa-list');
                    $item->weight(0);
                    // $item->append('admin.tasklist.timesheet.create');
                    $item->route('admin.tasklist.timesheet.index');
                    $item->authorize(
                        $this->auth->hasAccess('tasklist.timesheets.index')
                    );
                });
// append

            });
        });

        return $menu;
    }
}
