@extends('layouts.app');

@section('content')
    <div class="container">
        <div class="row"> {{-- works without the row --}}
            <div class="col-md-8 col-md-offset-2">
                <div class="page-header">
                    <h1>
                        {{ $profileUser->name }}
                        <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
                    </h1>
                </div>
                {{-- @foreach($profileUser->threads as $thread) --}}
                 {{-- @foreach($threads as $thread)  --}}
                 {{--  @foreach($activities as $activity)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="level">
                                <span class="flex"> 
                                    {{--
                                    @if($activity->type == 'created_thread')
                                        {{ $profileUser->name }} published a thread
                                    @endif
                                    @if($activity->type == 'created_reply')
                                        {{ $profileUser->name }} replied to thread
                                    @endif
                                    USE POLYMORPHISM INSTEAD :   --}}
                                    {{-- @include("profiles.activities.{$activity->type}") 
                                    
                                    {{-- <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted: 
                                     {<a href="{{ $thread->path() }}">{{ $thread->title }}</a> 
                                </span>
                                <span> --}}
                                        {{-- $thread->created_at->diffForHumans() --}} 
                                {{--</span>
                            </div>
                        </div>
                        <div class="panel-body"> --}}
                           {{-- { $thread->body --}}
                       {{-- { </div> 
                    </div>
                @endforeach   MOVED --}}
                
                {{-- @foreach($activities as $activity)
                    @include("profiles.activities.{$activity->type}") 
                @endforeach --}}
                @forelse($activities as $date => $activity)
                    {{-- @if($activity->where('type', '!=', 'created_favorite')->count() == 0)
                        @continue
                    @endif --}}
                    <h3 class="page-header">{{ $date }}</h3>
                    @foreach($activity as $record)
                        @if(view()->exists("profiles.activities.{$record->type}"))
                            @include("profiles.activities.{$record->type}", ['activity' => $record])
                        @endif
                    @endforeach
                @empty
                    <p>There is no actiity for this user yet.</p>
                @endforelse                
                {{-- {{ $threads->links() }} --}}
            </div>
        </div>
    </div>
@endsection


{{-- 
@component('profiles.alert')
    @slot('title')
        Forbidden
    @endslot

    You are not allowed to access this resource!
@endcomponent            
 --}}