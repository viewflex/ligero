
{{--TOP NAVBAR USED BY ALL DOMAINS AND LAYOUTS--}}

<nav class="navbar navbar-default">
    <div class="container-fluid">


        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="https://github.com/viewflex/ligero">
                <img alt="Brand" src="https://raw.githubusercontent.com/viewflex/ligero-docs/master/img/vf-icon.png">
            </a>
        </div>


        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav">
                <li class="dropdown">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{!! trans($trans_prefix.'ui.label.goto') !!} <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li @if ($domain == 'Items') class="disabled"@endif><a href="#">{!! trans($trans_prefix.'ui.label.items') !!}</a></li>
                        <li @if ($domain == 'Users') class="disabled"@endif><a href="#">{!! trans($trans_prefix.'ui.label.users') !!}</a></li>
                        <li role="separator" class="divider"></li>
                        <li @if ($domain == 'Reports') class="disabled"@endif><a href="#">{!! trans($trans_prefix.'ui.label.reports') !!}</a></li>
                        <li @if ($domain == 'Statistics') class="disabled"@endif><a href="#">{!! trans($trans_prefix.'ui.label.statistics') !!}</a></li>
                        <li role="separator" class="divider"></li>
                        <li @if ($domain == 'Settings') class="disabled"@endif><a href="#">{!! trans($trans_prefix.'ui.label.settings') !!}</a></li>
                    </ul>

                </li>
            </ul>

            @if ($query_info)
            <ul class="nav navbar-nav">
                <li class="dropdown">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{!! $domain !!} <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{!! strtolower($domain) !!}/create">{!! trans($trans_prefix.'ui.label.new_record') !!}</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="{!! $query_info['route'] !!}/json{!! $query_info['query'] !!}">{!! trans($trans_prefix.'ui.label.show_json') !!}</a></li>
                    </ul>

                </li>
            </ul>
            @endif


            <!-- Login/Register/Logout -->
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="/login">{!! trans($trans_prefix.'ui.label.login') !!}</a></li>
                    <li><a href="/register">{!! trans($trans_prefix.'ui.label.register') !!}</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/logout">{!! trans($trans_prefix.'ui.label.logout') !!}</a></li>
                        </ul>
                    </li>
                @endif
            </ul>

        </div>


    </div>
</nav>