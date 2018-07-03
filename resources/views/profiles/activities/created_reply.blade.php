{{--
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <span class="flex">
                {{ $profileUser->name }} replied to <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>
            </span>
            <span>
                {{ $activity->subject->created_at->diffForHumans() }}
            </span>
        </div>
    </div>
    <div class="panel-body">
        {{ $activity->subject->body }}
    </div>
</div>
--}}
@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} replied to
        <a href="{{ $activity->subject->thread->path() }}">{{ $activity->subject->thread->title }}</a>
    @endslot
    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent
