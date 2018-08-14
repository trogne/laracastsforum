<!--editing the question-->
<div class="panel panel-default" v-if="editing">
                    <div class="panel-heading">                    
                        <!--<a href="#">{{-- $thread->creatorName() --}}</a> posted:-->  <!--to follow Law of Demeter (anti-pattern) -->  
                        {{-- <a href="/profiles/{{ $thread->creator->name }}">{{ $thread->creator->name }}</a> posted: --}}
                        <div class="level">
                            <!--<input type="text" value="{{ $thread->title }}" class="form-control">-->
                            <input type="text" class="form-control" v-model="form.title">
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <!--<textarea class="form-control" rows="10">{{ $thread->body }}</textarea>-->
                            {{-- <textarea class="form-control" rows="10" v-model="form.body"></textarea> --}}
                            {{-- <wysiwyg v-model="form.body" :value="form.body"></wysiwyg> --}}
                            <wysiwyg v-model="form.body"></wysiwyg> <!-- can remove value because I accept value prop in Wysiwyg.vue-->
                        </div>
                    </div>
                        
                    <div class="panel-footer">
                        <div class="level">
                            <button class="btn btn-xs level-item" v-show="! editing" @click="editing = true">Edit</button>
                            <button class="btn btn-primary btn-xs level-item" v-show="editing" @click="update">Update</button>
                            <!--<button class="btn btn-xs level-item" @click="editing = false">Cancel</button>-->
                            <button class="btn btn-xs level-item" @click="resetForm">Cancel</button>
                            {{-- @if(Auth::check()) --}} {{-- @if (Auth::user()->can('update', $thread)) --}} @can('update', $thread)
                            <form action="{{ $thread->path() }}" method="POST" class="ml-a">
                                {{ csrf_field() }} {{ method_field('DELETE') }}
                        
                                <button type="submit" class="btn btn-link">Delete Thread</button>
                            </form>
                            @endcan
                        </div>
                    </div>
</div>

<!--viewing the question-->
<!--<div class="panel panel-default" v-if="! editing">-->
<div class="panel panel-default" v-else>
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
                            <img src="{{ $thread->creator->avatar_path }}"
                                alt="{{ $thread->creator->name }}"
                                width="25"
                                height="25"
                                class="mr-1">
                            
                            <span class="flex">
                                {{-- <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted: {{ $thread->title }}--}}
                                <!--<a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted: <span v-text="form.title"></span>-->
                                <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted: <span v-text="title"></span>
                            </span>
                        </div>
                    </div>
                    <!--<div class="panel-body">{{ $thread->body }}</div>-->
                    <!--<div class="panel-body" v-text="form.body"></div>-->
                    <div class="panel-body" v-html="body"></div> <!--cannot use Purify::clearn($body), because instead referencing a vue attribute-->
                        
                    <div class="panel-footer" v-if="authorize('owns', thread)">
                        <button class="btn btn-xs" @click="editing = true">Edit</button>
                    </div>
</div>
