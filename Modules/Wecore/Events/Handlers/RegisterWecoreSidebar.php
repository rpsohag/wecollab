<?php

namespace Modules\Wecore\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterWecoreSidebar implements \Maatwebsite\Sidebar\SidebarExtender
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
      $menu->group('Intranet v1.0', function (Group $group) {
        $GLOBALS['base_url'] = session('azienda') == 'We-COM' ? 'https://intranet.we-com.it/' : 'https://intranet.digitconsulting.it/';

        $group->authorize(
             $this->auth->hasAccess('wecore.intranet.v1')
        );

        $group->item('Agenda', function (Item $item) {
            $item->icon('fa fa-calendar');
            $item->weight(10);
            $item->url('https://intranet.we-com.it/?mod=mod_amministrazione&file=agenda_new');
            $item->authorize(
                 $this->auth->hasAccess('wecore.intranet.v1')
            );
        });

        /* $group->item('Richieste intervento (OLD)', function (Item $item) {
            $item->icon('fa fa-phone');
            $item->weight(10);
            $item->url('https://intranet.we-com.it/?mod=mod_pa_digitale&file=chiamate_registrate');
            $item->authorize(
                 $this->auth->hasAccess('wecore.intranet.v1')
            );
        }); */

        $group->item('Gestione personale', function (Item $item) {
            $item->icon('fa fa-calendar-plus-o');
            $item->weight(10);
            $item->url($GLOBALS['base_url'] . '?mod=mod_gestione_personale');
            $item->authorize(
                 $this->auth->hasAccess('wecore.intranet.v1')
            );
        });

      /*
        $group->item('Ambienti definitivi', function (Item $item) {
            $item->icon('fa fa-database');
            $item->weight(10);
            $item->url('https://intranet.we-com.it/?mod=mod_pa_digitale&file=ambienti_definitivi');
            $item->authorize(
                 $this->auth->hasAccess('wecore.intranet.v1')
            );
        });
      */
      });

//         $menu->group(trans('core::sidebar.content'), function (Group $group) {
//             $group->item(trans('wecore::wecores.title.wecores'), function (Item $item) {
//                 $item->icon('fa fa-copy');
//                 $item->weight(10);
//                 $item->authorize(
//                      /* append */
//                 );
//                 $item->item(trans('wecore::cores.title.cores'), function (Item $item) {
//                     $item->icon('fa fa-copy');
//                     $item->weight(0);
//                     $item->append('admin.wecore.core.create');
//                     $item->route('admin.wecore.core.index');
//                     $item->authorize(
//                         $this->auth->hasAccess('wecore.cores.index')
//                     );
//                 });
// // append
//
//             });
//         });

        return $menu;
    }
}
