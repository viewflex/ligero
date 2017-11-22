
{{--QUERY BAR FOR RESULTS PAGES--}}

<nav class="navbar navbar-default">
    <div class="container-fluid">

        <div class="btn-group">

            {!! $keyword_search !!}

            <ul class="nav navbar-nav">
                <li>
                    <a href="{!! strtolower($domain) !!}">All</a>
                </li>
            </ul>

            <ul class="nav navbar-nav">
                <li class="dropdown">
                    {!! $view_menu !!}
                </li>
            </ul>

        </div>

    </div>
</nav>

