<?php

namespace Modules\Commerciale\Events\Handlers;

use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Menu;
use Modules\Core\Events\BuildingSidebar;
use Modules\User\Contracts\Authentication;

class RegisterCommercialeSidebar implements \Maatwebsite\Sidebar\SidebarExtender
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
            $group->item('Commerciale', function (Item $item) {
                $item->icon('fa fa-briefcase');
                $item->weight(10);
                $item->authorize(
                     $this->auth->hasAccess('commerciale.offerte.index')
                     or $this->auth->hasAccess('commerciale.ordinativi.index')
                     // or $this->auth->hasAccess('commerciale.fatturazioni.index')
                     or $this->auth->hasAccess('commerciale.analisivendite.index')
                     or $this->auth->hasAccess('commerciale.censimenticlienti.index')
                     or $this->auth->hasAccess('commerciale.segnalazioniopportunita.index')
                     /* append */
                );
                $item->item(trans('commerciale::segnalazioniopportunita.title.segnalazioniopportunita'), function (Item $item) {
                    $item->icon('fa fa-flag');
                    $item->weight(0);
                   // $item->append('admin.commerciale.segnalazioneopportunita.create');
                    $item->route('admin.commerciale.segnalazioneopportunita.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.segnalazioniopportunita.index')
                    );
                });
                $item->item(trans('commerciale::censimenticlienti.title.censimenticlienti'), function (Item $item) {
                    $item->icon('fa fa-laptop');
                    $item->weight(0);
                    // $item->append('admin.commerciale.censimentocliente.create');
                    $item->route('admin.commerciale.censimentocliente.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.censimenticlienti.index')
                    );
                });
                $item->item(trans('tasklist::rinnovi.title.rinnovi'), function (Item $item) {
                    $item->icon('fa fa-check-circle');
                    $item->weight(0);
                    //$item->append('admin.tasklist.rinnovo.create');
                    $item->route('admin.tasklist.rinnovo.index');
                    $item->authorize(
                        $this->auth->hasAccess('tasklist.rinnovi.index')
                    );
                });
                $item->item(trans('commerciale::analisivendite.title.analisivendite'), function (Item $item) {
                    $item->icon('fa fa-check-square-o');
                    $item->weight(0);
                    // $item->append('admin.commerciale.analisivendita.create');
                    $item->route('admin.commerciale.analisivendita.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.analisivendite.index')
                    );
                });
                $item->item(trans('commerciale::offerte.title.offerte'), function (Item $item) {
                    $item->icon('fa fa-file-text');
                    $item->weight(0);
                   // $item->append('admin.commerciale.offerta.create');
                    $item->route('admin.commerciale.offerta.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.offerte.index')
                    );
                });
                $item->item(trans('commerciale::ordinativi.title.ordinativi'), function (Item $item) {
                    $item->icon('fa fa-list');
                    $item->weight(0);
                //    $item->append('admin.commerciale.ordinativo.create');
                    $item->route('admin.commerciale.ordinativo.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.ordinativi.index')
                    );
                });
                /*$item->item('Fatturazione', function (Item $item) {
                    $item->icon('fa fa-usd');
                    $item->weight(0);
                    //$item->append('admin.commerciale.fatturazione.create');
                    $item->route('admin.commerciale.fatturazione.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.fatturazioni.index')
                    );
                });
                $item->item(trans('commerciale::simaziendalis.title.simaziendalis'), function (Item $item) {
                    $item->icon('fa fa-vcard-o');
                    $item->weight(0);
                   // $item->append('admin.commerciale.simaziendali.create');
                    $item->route('admin.commerciale.simaziendali.index');
                    $item->authorize(
                        $this->auth->hasAccess('commerciale.simaziendalis.index')
                    );
                });*/
// append


            });
        });

        return $menu;
    }
}
