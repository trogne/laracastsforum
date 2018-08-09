@extends('layouts.app')

@section('header')
    <link rel="stylesheet" href="/css/vendor/jquery.atwho.css">
    <script>
        ////window.thread = @json($thread); //works too, json directiie
        //window.thread = <?= json_encode($thread) ?>; //if using "shared state"
    </script>
@endsection

@section('content')
<!--: we want to bind that, because we want the number rather than the string representation -->
<!--<thread-view :data-replies-count="{{ $thread->replies_count }}" :data-locked="{{ $thread->locked }}" inline-template> -->
<thread-view :thread="{{ $thread }}" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8"> {{-- removed col-md-offset-2 --}}
                <div class="panel panel-default">
                    <div class="panel-heading">                    
                        <!--<a href="#">{{-- $thread->creatorName() --}}</a> posted:-->  <!--to follow Law of Demeter (anti-pattern) -->  
                        {{-- <a href="/profiles/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> posted: --}}
                        <div class="level">
                            {{--
                                @if($thread->creator->avatar_path)
                                    <img src="{{ asset($thread->creator->avatar_path) }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-1">
                                @else
                                    <img src="https://www.gravatar.com/avatar/{{md5(strtolower(trim($thread->creator->email)))}}?d={{ urlencode("http://i.imgur.com/H357yaH.jpg") }} &s=40" width="25" height="25" class="mr-1">
                                @endif
                            --}}
                            {{-- <img src="{{ asset($thread->creator->avatar_path) }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-1"> --}}
                            {{-- <img src="{{ $thread->creator->avatar() }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-1"> --}}
                            <img src="{{ $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-1">
                            
                            <span class="flex">
                                <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted:
                                {{ $thread->title }}
                            </span>
                            {{-- @if(Auth::check()) --}}
                            {{-- @if (Auth::user()->can('update', $thread)) --}}
                            @can('update', $thread)
                                <form action="{{ $thread->path() }}" method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                    
                                    <button type="submit" class="btn btn-link">Delete Thread</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="panel-body">
                        {{ $thread->body }}
                    </div>
                </div>
                    
                {{-- @foreach($thread->replies as $reply) --}}
                {{-- @foreach($replies as $reply)
                    @include('threads.reply')
                @endforeach            
                {{ $replies->links() }}  --}}
    
                <!--<replies :data="{{ $thread->replies }}" @remove="repliesCount--" can-update="{{ Auth::check() && Auth::user()->can('upddate', $thread) }}"></replies>-->
                <!--<replies :data="{{ $thread->replies }}" @remove="repliesCount--" can-update="{{ Gate::allows('update', $thread) }}"></replies>-->
                <!--<replies :data="{{ $thread->replies }}  @added="repliesCount++" @remove="repliesCount--"></replies>"-->
                <replies @added="repliescount++" @remove="repliescount--"></replies>
                
                {{--@if($signIn)--}} {{--shared variable with all views, we don't have that yet--}}
                {{--Auth::check())--}}
                
                {{-- NOW Reply.vue
                @if(auth()->check())
                    {{-- auth()->user()->name --}} {{-- auth()->id() --}}
                {{-- <form action="{{ $thread->path().'/replies' }}" method="post"> {{-- route('addReply', $thread->id) , NOT url('addReply') --}}
                {{--    {{ csrf_field() }}
                        <div class="form-group">
                            <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5"></textarea> <!--tarea.form-control-->
                        </div>
                        <button type="submit" class="btn btn-default">Post</button>  <!--btn:s-->
                    </form>
                @else
                    <p class="text-center">Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>
                @endif
                --}}
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p>This thread was published {{ $thread->created_at->diffForHumans() }} by
                        <a href="#">{{ $thread->creator->name }}</a>, and currently
                        has {{-- $thread->replies->count() --}}
                        {{-- $thread->replies()->count() --}}
                        {{-- $thread->replyCount() --}}
                        {{-- $thread->replyCount --}}
                        {{-- $thread->replies_count --}}
                        <!--<span >@{{ repliesCount }}</span>-->
                        <span v-text="repliescount"></span>
                        {{ str_plural('comment', $thread->replies_count) }}
                        .</p> {{-- well we call a relationship as a property, laravel performs a sql query behind the scenes (return all replies, then count... but if replies(), sql query just fetch the count --}}
                    
                        <p>
                            <!--<subscribe-button :active="{{ $thread->isSubscribedTo ? 'true' : 'false' }}"></subscribe-button>-->
                            <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}"  v-if="signedIn"></subscribe-button>
                                
                            <!--<button class="btn btn-default" v-if="authorize('isAdmin') && ! locked" @click="locked = true">Lock</button>-->
                            <!--<button class="btn btn-default" v-if="authorize('isAdmin') && ! locked" @click="lock">Lock</button>-->
                            <!--two buttons : -->
                            <!--<button v-if="locked" class="btn btn-default" v-if="authorize('isAdmin')" @click="toggleLock">Unlock</button>-->
                            <!--<button v-else class="btn btn-default" @click="toggleLock">Lock</button>-->
                            <button class="btn btn-default"
                                v-if="authorize('isAdmin')"
                                @click="toggleLock"
                                v-text="locked ? 'Unlock' : 'Lock'">
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</thread-view>
@endsection
