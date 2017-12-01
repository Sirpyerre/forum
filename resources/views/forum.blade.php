@extends('layouts.app')

@section('content')
    @foreach($discussions as $d)
        <div class="panel panel-default">
            <div class="panel-heading">
                <img src="{{ $d->user->avatar }}" alt="" width="50px" height="40px">&nbsp;&nbsp;&nbsp;
                <span>
                    {{ $d->user->name }}, <b>{{ $d->created_at->diffForHumans() }}</b>
                </span>
                <a class="btn btn-default pull-right btn-xs" style="margin-left: 9px" href="{{ route('discussions', ['slug'=>$d->slug]) }}">
                    View
                </a>
                @if($d->hasBestAnswer())
                    <span class="pull-right btn btn-xs btn-success">
                        Cerrada
                    </span>
                @else
                    <span class="pull-right btn btn-xs btn-danger">
                        Abierta
                    </span>
                @endif
            </div>

            <div class="panel-body">
                <h4 class="text-center">
                    <b>{{ $d->title }}</b>
                </h4>
                <p class="text-center">
                    {{ str_limit($d->content, 50) }}
                </p>
            </div>

            <div class="panel-footer">
                <span>
                    {{ $d->replies->count() }} Replies
                </span>
                <a href="{{ route('channel', ['slug' =>$d->channel->slug]) }}"
                   class="pull-right btn btn-default btn-xs">
                    {{ $d->channel->title }}
                </a>
            </div>
        </div>
    @endforeach

    <div class="text-center">
        {{ $discussions->links() }}
    </div>
@endsection
