@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <img src="{{ $d->user->avatar }}" alt="" width="50px" height="40px">&nbsp;&nbsp;&nbsp;
            <span>
                {{ $d->user->name }}, <b>{{ $d->created_at->diffForHumans() }}</b>
            </span>
            
            @if($d->is_being_watched_by_auth_user())
                <a class="btn btn-default pull-right btn-xs"
                href="{{ route('discussion.unwatch', ['id' => $d->id]) }}">
                    Dejar de seguir
                </a>
            @else
                <a class="btn btn-default pull-right btn-xs"
                href="{{ route('discussion.watch', ['id' => $d->id]) }}">
                    Seguir
                </a>
            @endif
    </div>

    <div class="panel-body">
        <h4 class="text-center">
            <b>{{ $d->title }}</b>
        </h4>
        <hr>
        <p class="text-center">
            {{ $d->content }}    
        </p>
        <hr>
        @if($best_answer)
            <div class="text-center">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <img src="{{ $best_answer->user->avatar }}" alt="" width="50px" height="40px">&nbsp;&nbsp;&nbsp;
                        <span>
                            {{ $best_answer->user->name }}</b>
                        </span>
                    </div>
                    <div class="panel-body">
                        {{ $best_answer->content }}
                    </div>
                </div>
            </div>
            @endif
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
@foreach($d->replies as $r)
    <div class="panel panel-default">
        <div class="panel-heading">
            <img src="{{ $r->user->avatar }}" alt="" width="50px" height="40px">&nbsp;&nbsp;&nbsp;
            <span>
                {{ $r->user->name }}, <b>{{ $r->created_at->diffForHumans() }}</b>
            </span>
            @if(!$best_answer)
                <a href="/" class="btn btn-xs btn-info pull-right">Mark as best answer</a>
                @else
                <a href="/" class="btn btn-xs btn-info pull-right">Mark as best answer</a>
            @endif
        </div>

        <div class="panel-body">
            <p class="text-center">
                {{ $r->content }}    
            </p>
        </div>

        <div class="panel-footer">
            @if($r->is_liked_by_auth_user())
            <a href="{{ route('reply.unlike', ['id' => $r->id]) }}" class="btn btn-danger btn-xs">
                Unlike <span class="badge">{{ $r->like->count() }}</span>
            </a>
            @else
            <a href="{{ route('reply.like', ['id' => $r->id]) }}" class="btn btn-success btn-xs">
                like <span class="badge">{{ $r->like->count() }}</span>
            </a>
            @endif
        </div>
    </div>
@endforeach
    <div class="panel panel-default">
        <div class="panel-body">
            @if(Auth::check())
                <form action="{{ route('discussion.reply',['id' => $d->id]) }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="reply">Leave a reply...</label>
                        <textarea name="reply" id="reply" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success pull-rigth">Leave a reply</button>
                    </div>
                </form>
            @else 
                <div class="text-center">
                    <h2>Sing in to leave a reply</h2>
                </div>
            @endif
        </div>
    </div>
@endsection
