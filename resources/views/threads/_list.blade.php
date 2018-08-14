@forelse($threads as $thread)
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="level"> <!--every child of level with class flex wil lbe top to bottom-->
                <div class="flex">
                    <h4>
                        <!--<a href="/threads/{{ $thread->id }}">-->
                        <a href="{{ $thread->path() }}">
                            @if(auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                                <strong>
                                    {{ $thread->title }}
                                </strong>
                            @else
                                {{ $thread->title }}
                            @endif
                        </a>
                    </h4>
                    <h5>Posted By:
                        <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a>
                    </h5>
                </div>
                <a href="{{ $thread->path() }}">
                    {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}
                </a>
            </div>
        </div>
        <div class="panel-body">
            <!--<div class="body">{{ $thread->body }}</div>-->
            <div class="body">{!! $thread->body !!}</div>
        </div>
        <div class="panel-footer">
            {{-- {{ $thread->visits()->count() }} {{ str_plural('visit', $thread->visits()->count()) }} --}}
            {{ $thread->visits }} {{ str_plural('visit', $thread->visits) }}
        </div>
     </div>
@empty
    <p>There are no relevant records at this time.</p>
@endforelse