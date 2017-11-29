@extends($namespace.'::master')

@section('content')

    {{--ITEM INPUT FORM FOR CREATE AND UPDATE--}}

    <div class="container">
        <div class="row">

            {{--{!! $breadcrumbs !!}--}}

            <div class="col-md-12 col-md-offset-0">
                <div class="panel panel-default">


                    <div class="panel-heading">
                        @if ($read_only)
                            <a href="{{ $back_to }}"><button tabindex="1" class="btn btn-primary btn-sm active" role="button">{!! trans($trans_prefix.'ui.label.back') !!}</button></a>
                        @else
                            <a href="{{ $back_to }}"><button tabindex="8" class="btn btn-default btn-sm" role="button">{!! trans($trans_prefix.'ui.label.cancel') !!}</button></a>
                        @endif

                        &nbsp;&nbsp;
                        {!! $info !!} @if ($message)&nbsp;&nbsp;{!! trans($trans_prefix.'ui.label.message') !!}:&nbsp;{!! $message !!}@endif
                    </div>

                    @if ($errors->any())
                    <div class="panel-heading">

                        <h3>{!! trans($trans_prefix.'ui.label.errors') !!}:</h3>
                        <ul  class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                    </div>
                    @endif


                    <div class="panel-body">

                        <form class="form-horizontal" method="post" action="{{ $form_action }}" name="fEditItem">
                            {{ csrf_field() }}

                            @if($action_method == 'create')
                                {{ method_field('POST') }}
                            @else
                                {{ method_field('PUT') }}
                                <input type="hidden" name="id" value="{{ $item['id'] or '' }}">
                            @endif

                            <input type="hidden" name="active" value="0">


                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">{!! trans($trans_prefix.'ui.name') !!}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $item['name'] or '' }}" placeholder="{!! trans($trans_prefix.'ui.name') !!}" tabindex="1"{{ $read_only ? ' disabled' : '' }}>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-3">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"@if ($item){{ $item['active'] ? ' checked' : '' }}@endif value="1" tabindex="2" name="active"{{ $read_only ? ' disabled' : '' }}> {!! trans($trans_prefix.'ui.active_true') !!}
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="category" class="col-sm-2 control-label">{!! trans($trans_prefix.'ui.category') !!}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="category" name="category" value="{{ $item['category'] or '' }}" placeholder="{!! trans($trans_prefix.'ui.category') !!}" tabindex="3"{{ $read_only ? ' disabled' : '' }}>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="subcategory" class="col-sm-2 control-label">{!! trans($trans_prefix.'ui.subcategory') !!}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="subcategory" name="subcategory" value="{{ $item['subcategory'] or '' }}" placeholder="{!! trans($trans_prefix.'ui.subcategory') !!}" tabindex="4"{{ $read_only ? ' disabled' : '' }}>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">{!! trans($trans_prefix.'ui.description') !!}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="description" name="description" value="{{ $item['description'] or '' }}" placeholder="{!! trans($trans_prefix.'ui.description') !!}" tabindex="5"{{ $read_only ? ' disabled' : '' }}>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="price" class="col-sm-2 control-label">{!! trans($trans_prefix.'ui.price') !!}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="price" name="price" value="{{ $item['price'] or '' }}" placeholder="{!! trans($trans_prefix.'ui.price') !!}" tabindex="6"{{ $read_only ? ' disabled' : '' }}>
                                </div>
                            </div>


                            @if (!$read_only)
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-6">
                                        <button type="submit" name="action" value="save" tabindex="7" class="btn btn-primary">{!! trans($trans_prefix.'ui.label.save') !!}</button>
                                    </div>


                                </div>
                            @endif


                        </form>

                    </div>


                </div>
            </div>


        </div>
    </div>


@endsection