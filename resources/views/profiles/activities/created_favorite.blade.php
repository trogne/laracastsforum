@component('profiles.activities.activity')
    @slot('heading')
        <a href="{{ $activity->subject->favorited->path() }}">
            {{ $profileUser->name }} favorited a reply {{--strtolower((new \ReflectionClass($activity->subject->favorited))->getShortName())--}}
        </a>
        {{-- <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a> --}}
    @endslot
    @slot('body')
        <span style='color:red'>&hearts;</span> {{ $activity->subject->favorited->body }} <span style='color:red'>&hearts;</span>
    @endslot
@endcomponent
