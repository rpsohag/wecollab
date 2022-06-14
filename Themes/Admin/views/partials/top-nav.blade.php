@php
    //$tasks =    \Modules\Tasklist\Entities\Attivita::with(['users'])->where(function($query) {
    //                $query->where(function ($q) {
    //                    $q->where('richiedente_id', Auth::id())
    //                    ->orWhereHas('users', function($assegnatari) {
    //                        $assegnatari->where('users.id', Auth::id());
    //                    })->orWhereJsonContains('supervisori_id->'.Auth::id().'->user_id', (string)Auth::id());
    //                });
    //            })->where('stato', 0)->get(); 

    $tasks = \Modules\Tasklist\Entities\Attivita::with(['users'])->whereHas('users', function($assegnatari) {
                                                                    $assegnatari->where('users.id', Auth::id());
                                                                })->where('stato', 0)->get();

    $tasks = $tasks->filter(function($att) {
                return $att->hasRequisiti();
    });
@endphp 

<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="navbar-btn sidebar-toggle hide-desktop" data-toggle="push-menu" role="button" style="margin: 0;">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>

    @if($currentUser->switch_is_active)
        <a href="{{ route('admin.profile.profile.switchAzienda', ((get_azienda() === 'we-com') ? 'digit consulting' : 'we-com')) }}" class="navbar-btn navbar-switch" role="button" style="color: #fff;" data-toggle="tooltip" data-original-title="Cambia Azienda" data-placement="bottom">
            <span class="fa-stack">
                <i class="fa fa-square-o fa-stack-2x"></i>
                <i class="fa fa-refresh fa-stack-1x"> </i>
            </span>
        </a>
    @endif

    @if(auth_user()->hasAccess('wecore.intranet.v1'))
        <form action="{{ (get_azienda() === 'we-com' ? 'https://intranet.we-com.it/inc/processi/login_by_new.php' : 'https://intranet.digitconsulting.it/inc/processi/login_by_new.php') }}" method="post" target="_blank">
        <input type="hidden" name="user_id" value="{{ $currentUser->id }}">
        <input type="hidden" name="user_email" value="{{ $currentUser->email }}">
        <input type="hidden" name="wecollab_token" value="000a113d-2852-4b0a-9ce8-47f1135ba95c">

        <button type="submit" class="navbar-btn navbar-old-intranet" style="color: #fff; border: none;" data-toggle="tooltip" data-original-title="Apri la versione precedente" data-placement="bottom">
            <span class="fa-stack">
            <i class="fa fa-square-o fa-stack-2x"></i>
            <i class="fa fa-external-link"></i>
            </span>
        </button>
        </form>
    @endif

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li>
              <a href="#">{{ session('azienda') }}</a>
            </li>
            <?php if (is_module_enabled('Notification')): ?>
            @include('notification::partials.notifications')
            <?php endif; ?>
            <li>
                <a href="" class="publicUrl" style="display: none">
                    <i class="fa fa-eye"></i> {{ trans('page::pages.view-page') }}
                </a>
            </li>
            {{-- <li><a href="{{ url('/') }}"><i class="fa fa-eye"></i> {{ trans('core::core.general.view website') }}</a></li> --}}
            <li class="hidden" class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-flag"></i>
                    <span>
                        {{ LaravelLocalization::getCurrentLocaleName()  }}
                        <i class="caret"></i>
                    </span>
                </a>
                <ul class="dropdown-menu language-menu">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <li class="{{ App::getLocale() == $localeCode ? 'active' : '' }}">
                            <a rel="alternate" lang="{{$localeCode}}" href="{{LaravelLocalization::getLocalizedURL($localeCode) }}">
                                {!! $properties['native'] !!}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning">{{ Auth::user()->unreadNotifications()->count() }}</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="header">Hai {{ Auth::user()->unreadNotifications()->count() }} notifiche non lette.</li>
                    <li>
                        <ul class="menu">
                    @foreach(Auth::user()->unreadNotifications()->get() as $notification)
                        <li>
                            <a href="{{ !empty($notification->data['url']) ? $notification->data['url'] : '' }}">
                                @if($notification->data['tipologia'] == 'Nuovo Ticket')
                                    <i class="fa fa-ticket text-aqua"></i> Ti è stato assegnato un nuovo ticket!<br><span style="margin-left:24px;"> Codice: {{ $notification->data['codice']}} </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
                <li class="footer"><a href="{{ route('admin.account.profile.notifiche.markasread') }}">Segna come lette</a></li>
                </ul>
            </li>
            <li class="dropdown tasks-menu" data-toggle="tooltip" data-original-title="Hai {{ $tasks->count() }} attività" data-placement="left">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-tasks"></i>
                <span class="label label-danger">{{ $tasks->count() }}</span>
              </a>
              <ul class="dropdown-menu">
                <li class="header">Hai {{ $tasks->count() }} attività</li>
                <li>
                  <!-- inner menu: contains the actual data -->
                  <ul class="menu">
                    @foreach ($tasks->take(3) as $key => $task)
                    <li><!-- Task item -->
                      <a href="{{ route('admin.tasklist.attivita.edit', $task->id) }}">
                        <h3>
                          {{ $task->oggetto }}
                          <small class="pull-right">{{ $task->percentuale_completamento }}%</small>
                        </h3>
                        <div class="progress xs">
                          <div class="progress-bar progress-bar-aqua" style="width: {{ $task->percentuale_completamento }}%" role="progressbar" aria-valuenow="{{ $task->percentuale_completamento }}" aria-valuemin="0" aria-valuemax="100">
                            <span class="sr-only">{{ $task->percentuale_completamento }}% Completato</span>
                          </div>
                        </div>
                      </a>
                    </li>
                    <!-- end task item -->
                    @endforeach
                  </ul>
                </li>
                <li class="footer">
                  <a href="{{ route('admin.tasklist.attivita.index') }}">Vedi tutte le attività</a>
                </li>
              </ul>
            </li>
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-user"></i>
                    <span>
                        <?php if ($user->present()->fullname() != ' '): ?>
                            {{ $user->present()->fullName() }}
                        <?php else: ?>
                            <em>{{trans('core::core.general.complete your profile')}}.</em>
                        <?php endif; ?>
                        <i class="caret"></i>
                    </span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header bg-light-blue">
                        <img src="{{ $user->present()->gravatar() }}" class="img-circle" alt="User Image" />
                        <p>
                            <?php if ($user->present()->fullname() != ' '): ?>
                                {{ $user->present()->fullname() }}
                            <?php else: ?>
                                <em>{{trans('core::core.general.complete your profile')}}.</em>
                            <?php endif; ?>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="{{ route('admin.account.profile.edit') }}" class="btn btn-default btn-flat">
                                {{ trans('core::core.general.profile') }}
                            </a>
                        </div>
                        <div class="pull-right">
                            <a href="{{ route('logout') }}" class="btn bg-red btn-flat">
                                {{ trans('core::core.general.sign out') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
