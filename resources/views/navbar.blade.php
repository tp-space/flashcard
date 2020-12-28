<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

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
                <li class="nav-item {{ Request::is('labels') ? 'active' : '' }}">
                    <a class="nav-link" href="/labels">Labels <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item {{ Request::is('cards') ? 'active' : '' }}">
                    <a class="nav-link" href="/cards">Cards <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item {{ Request::is('examples') ? 'active' : '' }}">
                    <a class="nav-link" href="/examples">Examples <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item {{ Request::is('quiz') ? 'active' : '' }}">
                    <a class="nav-link" href="/quiz">Quiz <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item {{ Request::is('configs') ? 'active' : '' }}">
                    <a class="nav-link" href="/configs">Config <span class="sr-only">(current)</span></a>
                </li>
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
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
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

