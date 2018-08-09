{{-- MOVED TO VUE !!! --}}
{{-- inline-template : yes I want a vue component, but the template for it is actually going to be inline --}}
{{-- FINI, now an full vue component !!!! --}}
<reply :data="{{ $reply }}" inline-template v-cloak>  {{-- : for v-bind:,   to pass as json  ;  v-cloak removed when everything is loaded --}}  
    <div id="reply-{{ $reply->id }}" class="panel panel-default">
        <div class="panel-heading">
            <div class="level">
                <h5 class="flex">
                    {{-- <a href="/profiles/{{ $reply->owner->name }}"> --}}
                    <a href="{{ route('profile', $reply->owner) }}"> {{-- ou $reply->owner->name, then route model binding... --}}
                        {{ $reply->owner->name }}
                    </a> said {{ $reply->created_at->diffForHumans() }}...
                </h5>
                @if(Auth::check())
                <div>
                    {{-- <form method="POST" action="/replies/{{ $reply->id }}/favorites">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-default" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                            {{-- $reply->favorites()->count() }} {{ str_plural('Favorite', $reply->favorites()->count()) --}}
                            {{-- $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) 
                            {{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
                        </button>
                    </form> --}}
                    <favorite :reply="{{ $reply }}"></favorite>
                </div>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body">@{{ body }}</textarea> {{-- v-model and v-text --}}
                </div>
                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>  <!--v-text au lieu de {{ $reply->body }}-->
        </div>
        
        {{-- @auth @endauth --}}
        @can('update', $reply)
            <div class="panel-footer level">  
                <button class="btn btn-xs mr-1" @click="editing = true">Edit</button>
                {{--<form method="POST" action="/replies/{{ $reply->id }}">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form>--}}
                <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
            </div>
        @endcan
        
    </div>
</reply>
