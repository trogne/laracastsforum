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
            <div class="panel panel-default">
                <div class="panel-heading">
                    Search
                </div>
                <div class="panel-body">
                    <form method="GET" action="/threads/search">
                        <div class="form-group">
                            <input type="text" placeholder="Search for something..." name="q" class="form-control">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-default" type="submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>        
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
