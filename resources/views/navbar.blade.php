<nav class="navbar navbar-expand-sm navbar-dark bg-dark">

    <div class="container">
        <a class="navbar-brand" href="/">Flashcard</a>
        <button class="navbar-toggler" 
                type="button" 
                data-toggle="collapse" 
                data-target="#tp_nav" 
                aria-controls="tp_nav" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="tp_nav">
            <ul class="navbar-nav mr-auto">

                @php ($sel = \App\Http\Controllers\FilterController::getState()["tp_app"])

                @php ($app = \App\Http\Controllers\MainController::MAIN_LABEL)
                <li class="nav-item {{ $sel == $app ? 'active' : '' }}">
                    <a class="nav-link tp_link" data-toggle="collapse" data-target=".navbar-collapse.show" href="#" data-app="{{ $app }}">
                        Labels<span class="sr-only">(current)</span>
                    </a>
                </li>

                @php ($app = \App\Http\Controllers\MainController::MAIN_CARD)
                <li class="nav-item {{ $sel == $app ? 'active' : '' }}">
                    <a class="nav-link tp_link" data-toggle="collapse" data-target=".navbar-collapse.show" href="#" data-app="{{ $app }}">
                        Cards<span class="sr-only">(current)</span>
                    </a>
                </li>

                @php ($app = \App\Http\Controllers\MainController::MAIN_EXAMPLE)
                <li class="nav-item {{ $sel == $app ? 'active' : '' }}">
                    <a class="nav-link tp_link" data-toggle="collapse" data-target=".navbar-collapse.show" href="#" data-app="{{ $app }}">
                        Examples<span class="sr-only">(current)</span>
                    </a>
                </li>

                @php ($app = \App\Http\Controllers\MainController::MAIN_QUIZ)
                <li class="nav-item {{ $sel == $app ? 'active' : '' }}">
                    <a class="nav-link tp_link" data-toggle="collapse" data-target=".navbar-collapse.show" href="#" data-app="{{ $app }}">
                        Quiz<span class="sr-only">(current)</span>
                    </a>
                </li>
{{--
                @php ($app = \App\Http\Controllers\MainController::MAIN_STATS)
                <li class="nav-item {{ $sel == $app ? 'active' : '' }}">
                    <a class="nav-link tp_link" data-toggle="collapse" data-target=".navbar-collapse.show" href="#" data-app="{{ $app }}">
                        Stats<span class="sr-only">(current)</span>
                    </a>
                </li>
--}}
            </ul>
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    
                    {{-- Allow guest registration only for the first user --}}
                    @if (Route::has('register') && (\App\Models\User::count() == 0))
                        <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a 
                            id="navbarDropdown" 
                            class="nav-link dropdown-toggle" 
                            href="#" 
                            role="button" 
                            data-toggle="dropdown" 
                            aria-haspopup="true" 
                            aria-expanded="false" 
                            v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @if (Route::has('register'))
                                <a class="dropdown-item" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

