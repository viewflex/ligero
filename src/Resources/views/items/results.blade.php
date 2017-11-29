@extends($namespace.'::master')

@section('content')

    {{--RESULTS DISPLAY LAYOUT WITH QUERY CONTROLS, INFO HEADER AND PAGE NAVIGATION--}}

    <div class="container">
        <div class="row">


            @include($namespace.'::partials.querybar')


            <!-- RESULTS DISPLAY PANEL -->
            <div class="panel panel-default">


                <div class="panel-heading">
                    {!! $info !!}
                    @if ($message)&nbsp;&nbsp;<b>{!! $message !!}</b>@endif

                    @if($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li >{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>



                {{--DISPLAY RESULTS AS SPECIFIED BY 'view' QUERY PARAMETER (list|grid|item)--}}

                @if ($query_info['view'] === 'list')

                <div>
                    <table class="table table-hover table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th width="40">{!! trans($trans_prefix.'ui.id') !!}</th>
                                <th>{!! trans($trans_prefix.'ui.name') !!}</th>
                                <th>{!! trans($trans_prefix.'ui.category') !!}</th>
                                <th>{!! trans($trans_prefix.'ui.subcategory') !!}</th>
                                <th>{!! trans($trans_prefix.'ui.price') !!}</th>
                                <th width="20"></th>
                                <th width="20"></th>
                            </tr>
                        </thead>
                        <tbody>

                        @foreach ($items as $index => $item)

                            <tr>
                                <th scope="row">{!! $item['id'] !!}</th>
                                <td>{!! $item['name'] !!}</td>
                                <td>{!! $item['category'] !!}</td>
                                <td>{!! $item['subcategory'] !!}</td>
                                <td>{!! $item['price'] !!} / {!! $item['alt_price'] !!}</td>
                                <td>
                                    <div class="btn-group btn-group-xs" role="group" aria-label="Edit">
                                        <a href="{{ ($path.'/'.strtolower($domain).'/'.$item['id'].'/edit') }}" role="button"  class="btn btn-default btn-xs">{!! trans($trans_prefix.'ui.label.edit') !!}</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs" role="group" aria-label="Delete">
                                        <form method="post" action="{{ ($path.'/'.strtolower($domain).'/'.$item['id']) }}" name="fDeleteItem" onsubmit="return confirm('{!! trans($trans_prefix.'ui.prompt.confirm_record_delete') !!}');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <input type="submit" name="submit" value="Delete" role="button" class="btn btn-default btn-xs">
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                </div>

                @elseif ($query_info['view'] === 'grid')

                <div class="panel-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td align="center">

                                    @foreach ($items as $index => $item)

                                        <div class="item-wrapper-grid">
                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    <h6>{!! $item['name'] !!}</h6>
                                                    <small>{!! $item['price'] !!} / {!! $item['alt_price'] !!}</small>

                                                </div>
                                            </div>
                                        </div>

                                    @endforeach

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @else

                <div class="panel-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td align="center">

                                    @foreach ($items as $index => $item)
                                        <table class="table table-hover table-bordered table-condensed">

                                            <tbody>

                                                <tr>
                                                    <td width="15%">{!! trans($trans_prefix.'ui.id') !!}</td>
                                                    <td>{!! $item['id'] !!}</td>
                                                </tr>

                                                <tr>
                                                    <td>{!! trans($trans_prefix.'ui.name') !!}</td>
                                                    <td>{!! $item['name'] !!}</td>
                                                </tr>

                                                <tr>
                                                    <td>{!! trans($trans_prefix.'ui.category') !!}</td>
                                                    <td>{!! $item['category'] !!}</td>
                                                </tr>

                                                <tr>
                                                    <td>{!! trans($trans_prefix.'ui.subcategory') !!}</td>
                                                    <td>{!! $item['subcategory'] !!}</td>
                                                </tr>

                                                <tr>
                                                    <td>{!! trans($trans_prefix.'ui.description') !!}</td>
                                                    <td>{!! $item['description'] !!}</td>
                                                </tr>

                                                <tr>
                                                    <td>{!! trans($trans_prefix.'ui.price') !!}</td>
                                                    <td>{!! $item['price'] !!} / {!! $item['alt_price'] !!}</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    @endforeach

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @endif


                @include($namespace.'::partials.pagenav')


            </div> <!-- end results display panel-->


        </div> <!-- end row -->
    </div> <!-- end container -->


@endsection