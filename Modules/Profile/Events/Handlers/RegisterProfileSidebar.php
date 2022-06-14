<?php

namespace Modules\Profile\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterProfileSidebar implements \Maatwebsite\Sidebar\SidebarExtender
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
        //$sidebar->add($this->extendWith($sidebar->getMenu()));
    }

    /**
     * @param Menu $menu
     * @return Menu
     */
    public function extendWith(Menu $menu)
    {
        $menu->group(trans('workshop::workshop.title'), function (Group $group) {
            $group->item(trans('user::users.title.users'), function (Item $item) {
                $item->weight(10);
                $item->icon('fa fa-users');
                $item->authorize(
                    $this->auth->hasAccess('user.users.index') or $this->auth->hasAccess('user.roles.index')
                );

                $item->item(trans('profile::procedure.title.procedura'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.profile.procedura.create');
                    $item->route('admin.profile.procedure.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.procedura.index')
                    );
                });

                $item->item(trans('profile::aree.title.area'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.profile.area.create');
                    $item->route('admin.profile.aree.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.area.index')
                    );
                });

                $item->item(trans('profile::gruppos.title.gruppos'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.profile.gruppo.create');
                    $item->route('admin.profile.gruppo.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.gruppi.index')
                    );
                });

                $item->item(trans('profile::figureprofessionali.title.figureprofessionali'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.profile.figuraprofessionale.create');
                    $item->route('admin.profile.figuraprofessionale.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.figureprofessionali.index')
                    );
                });
            });
        });

        $menu->group(trans('core::sidebar.content'), function (Group $group) {
            $group->item(trans('profile::profiles.title.profiles'), function (Item $item) {
                $item->icon('fa fa-copy');
                $item->weight(10);
                $item->authorize(
                     /* append */
                );
                $item->item(trans('profile::profiles.title.profiles'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                    $item->append('admin.profile.profile.create');
                    $item->route('admin.profile.profile.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.profiles.index')
                    );
                });
// append
            });
        });

        return $menu;
    }
}
