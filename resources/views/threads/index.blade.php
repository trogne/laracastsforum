@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            {{-- @foreach($threads as $thread) --}}
            @forelse($threads as $thread)
               <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="level">
                            <h4 class="flex">
                                <!--<a href="/threads/{{ $thread->id }}">-->
                                <a href="{{ $thread->path() }}">
                                    {{ $thread->title }}
                                </a>
                            </h4>
                            <a href="{{ $thread->path() }}">
                                {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}
                            </a>
                        </div>                        
                    </div>
                    <div class="panel-body">
                        <div class="body">{{ $thread->body }}</div>
                    </div>
                </div>
            @empty
                <p>There are no relevant records at this time.</p>
            @endforelse
            {{-- 
            @if($popular)
                {{ $threads->appends(['popular' => '1'])->links() }}
            @else
                {{ $threads->links() }}                        
            @endif
             --}}
        </div>
    </div>
</div>
@endsection
