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
        </div>
    </div>
</nav>

