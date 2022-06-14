<?php

namespace Modules\Amministrazione\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterAmministrazioneSidebar implements \Maatwebsite\Sidebar\SidebarExtender
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
            $group->item(trans('amministrazione::clienti.title.clienti'), function (Item $item) {
                $item->icon(config('amministrazione.clienti.sidebar.icon'));
                $item->weight(0);
              //  $item->append('admin.amministrazione.clienti.create');
                $item->route('admin.amministrazione.clienti.index');
                $item->authorize(
                    $this->auth->hasAccess('amministrazione.clienti.index')
                );
            });

            $group->item(config('amministrazione.name'), function (Item $item) {
                $item->icon('fa fa-database');
                $item->weight(10);
                $item->authorize(
                    $this->auth->hasAccess('amministrazione.sidebar')
                    or $this->auth->hasAccess('commerciale.simaziendalis.index')
                    or $this->auth->hasAccess('commerciale.fatturazioni.index')
                     /* append */
                );
                $item->item('Fatturazione', function (Item $item) {
                    $item->icon('fa fa-usd');
                    $item->weight(0);
                    //$item->append('admin.commerciale.fatturazione.create');
                    $item->route('admin.commerciale.fatturazione.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.fatturazioni.index')
                    );
                });
                $item->item('Sim Aziendali', function (Item $item) {
                    $item->icon('fa fa-vcard-o');
                    $item->weight(0);
                   // $item->append('admin.commerciale.simaziendali.create');
                    $item->route('admin.commerciale.simaziendali.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.simaziendalis.index')
                    );
                });
                $item->item('Beni IT', function (Item $item) {
                    $item->icon('fa fa-desktop');
                    $item->weight(0);
                   // $item->append('admin.commerciale.simaziendali.create');
                    $item->route('admin.amministrazione.benistrumentali.index');
                    $item->authorize(
                        $this->auth->hasAccess('amministrazione.benistrumentali.index')
                    );
                });
                // $item->item(trans('amministrazione::clientereferentis.title.clientereferentis'), function (Item $item) {
                //     $item->icon('fa fa-copy');
                //     $item->weight(0);
                //     $item->append('admin.amministrazione.clientereferenti.create');
                //     $item->route('admin.amministrazione.clientereferenti.index');
                //     $item->authorize(
                //         $this->auth->hasAccess('amministrazione.clientereferentis.index')
                //     );
                // });
// append



            });
        });

        return $menu;
    }
}
