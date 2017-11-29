@extends('layouts.app')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">Edit channel: {{ $channel->title }}</div>

    <div class="panel-body">
        <form method="post" action="{{ route('channels.update', ['id'=>$channel->id]) }}">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="form-group">
                <input type="text" class="form-control" name="channel" value="{{ $channel->title }}">
            </div>

            <div class="form-group">
                <div class="text-center">
                    <button class="btn btn-success" type="submit">
                        Edit channel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
