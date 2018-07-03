{{--
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="level">
            <span class="flex">
                {{ $profileUser->name }} published <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>
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


{{-- 
@include('profiles.activities.activity', [
    'heading' => 'my heading',
    'body' => 'my body'    
])
--}}

@component('profiles.activities.activity')
    @slot('heading')
        {{ $profileUser->name }} published
        <a href="{{ $activity->subject->path() }}">{{ $activity->subject->title }}</a>
    @endslot
    @slot('body')
        {{ $activity->subject->body }}
    @endslot
@endcomponent

{{-- or without slot/endslot if variable $slot --}}