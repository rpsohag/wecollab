<?php

namespace Modules\User\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Sidebar\AbstractAdminSidebar;

class RegisterUserSidebar extends AbstractAdminSidebar
{
    /**
     * Method used to define your sidebar menu groups and items
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

                $item->item(trans('user::users.title.users'), function (Item $item) {
                    $item->weight(0);
                    $item->icon('fa fa-users');
                    $item->route('admin.user.user.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.users.index')
                    );
                });

                $item->item(trans('user::roles.title.roles'), function (Item $item) {
                    $item->weight(1);
                    $item->icon('fa fa-flag-o');
                    $item->route('admin.user.role.index');
                    $item->authorize(
                        $this->auth->hasAccess('user.roles.index')
                    );
                });

                $item->item(trans('profile::procedure.title.procedure'), function (Item $item) {
                    $item->icon('fa fa-folder');
                    $item->weight(0);
                  //  $item->append('admin.profile.procedura.create');
                    $item->route('admin.profile.procedure.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.procedura.index')
                    );
                });

                $item->item(trans('profile::aree.title.aree'), function (Item $item) {
                    $item->icon('fa fa-copy');
                    $item->weight(0);
                 //   $item->append('admin.profile.area.create');
                    $item->route('admin.profile.aree.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.area.index')
                    );
                });

                $item->item(trans('profile::gruppi.title.gruppi'), function (Item $item) {
                    $item->icon('fa fa-sitemap');
                    $item->weight(0);
                 //   $item->append('admin.profile.gruppo.create');
                    $item->route('admin.profile.gruppo.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.gruppi.index')
                    );
                });

                $item->item(trans('profile::figureprofessionali.title.figureprofessionali'), function (Item $item) {
                    $item->icon('fa fa-briefcase');
                    $item->weight(0);
                    //$item->append('admin.profile.figuraprofessionale.create');
                    $item->route('admin.profile.figuraprofessionale.index');
                    $item->authorize(
                        $this->auth->hasAccess('profile.figureprofessionali.index')
                    );
                });
            });
        });
        $menu->group('Account', function (Group $group) {
            $group->weight(110);
            $group->item('Profilo', function (Item $item) {
                $item->weight(10);
                $item->icon('fa fa-user');
				$item->item('I tuoi Dati', function (Item $item) {
                    $item->weight(10);
                    $item->icon('fa fa-vcard-o');
					$item->route('admin.account.profile.edit');
                });
				// $item->item('Le tue Richieste', function (Item $item) {
                //     $item->weight(10);
                //     $item->icon('fa fa-envelope-o');
				// 	$item->route('admin.account.richieste.index',['tab'=>1]);
                // });
            });
            $group->item(trans('user::users.api-keys'), function (Item $item) {
                $item->weight(1);
                $item->icon('fa fa-key');
                $item->route('admin.account.api.index');
                $item->authorize(
                    $this->auth->hasAccess('account.api-keys.index')
                );
            });
        });

        return $menu;
    }
}
