@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- <div class="col-md-8 col-md-offset-2"> --}}
        <div class="col-md-8">
            {{-- @foreach($threads as $thread) --}}
            {{-- @include('threads._list', ['threads' => $threadsByFiso]) --}}
            @include('threads._list')
            {{-- {{ $threads->links() }} --}}  {{-- get_class($threads) --}} {{-- Illuminate\Pagination\LengthAwarePaginator --}}
            @if($popular)
                {{ $threads->appends(['popular' => '1'])->links() }} {{-- appends to the query --}}
            @else
                {{ $threads->links() }}                        
            @endif
        </div>
        <div class="col-md-4">
            @if(count($trending))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Trending Threads
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            @foreach($trending as $thread)
                                <li class="list-group-item">
                                    <a href="{{ url($thread->path) }}">
                                        {{ $thread->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>                
            @endif
        </div>            
    </div>
</div>
@endsection
