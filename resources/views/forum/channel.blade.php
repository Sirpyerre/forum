@extends('layouts.forum')

@section('title', $channel->title)
@section('description', $channel->description ?? "Browse discussions in {$channel->title}. Join the conversation and share your knowledge.")

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Channels -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Channels</h2>

                <!-- Search -->
                <div class="relative mb-4">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text"
                           placeholder="Search channels..."
                           class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <!-- Channel List -->
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('forum.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <span class="text-sm">All Discussions</span>
                        </a>
                    </li>
                    @foreach($channels as $ch)
                        <li>
                            <a href="{{ route('channel.show', $ch) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg {{ $ch->id === $channel->id ? 'bg-indigo-600 text-white font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                                <span class="text-sm">{{ $ch->title }}</span>
                                <span class="text-xs {{ $ch->id === $channel->id ? 'text-indigo-200' : 'text-gray-500 dark:text-gray-400' }}">{{ $ch->discussions_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                @auth
                    @if(auth()->user()->admin)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.channels.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                Manage Channels
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Forum Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Forum Stats</h2>

                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Discussions</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($totalDiscussions ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Active Members</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($activeMembers ?? 0) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Today's Posts</dt>
                        <dd class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ number_format($todaysPosts ?? 0) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Main Content - Discussions -->
        <div class="lg:col-span-3">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $channel->title }}</h1>
                @if($channel->description)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $channel->description }}</p>
                @endif
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $discussions->total() }} {{ $discussions->total() === 1 ? 'discussion' : 'discussions' }}</p>
            </div>

            @if($discussions->count() > 0)
                <!-- Discussions List -->
                <div class="space-y-4">
                    @foreach($discussions as $discussion)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition">
                            <div class="flex gap-4">
                                <!-- Reply Count -->
                                <div class="flex flex-col items-center justify-center w-16 flex-shrink-0">
                                    <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $discussion->replies_count }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">replies</div>
                                </div>

                                <!-- Discussion Content -->
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('discussions.show', $discussion) }}" class="group">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                            {{ $discussion->title }}
                                        </h3>
                                    </a>

                                    <div class="mt-3 flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                        <!-- Avatar & Author -->
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center w-6 h-6 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-full text-xs font-semibold">
                                                {{ strtoupper(substr($discussion->user->name, 0, 2)) }}
                                            </div>
                                            <span class="font-medium">{{ $discussion->user->name }}</span>
                                        </div>

                                        <!-- Channel Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ $discussion->channel->title }}
                                        </span>

                                        <!-- Views -->
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            {{ $discussion->views }} views
                                        </span>

                                        <!-- Time -->
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $discussion->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($discussions->hasPages())
                    <div class="mt-6 flex justify-center">
                        <nav class="inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                            @if($discussions->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 cursor-not-allowed rounded-l-lg">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $discussions->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-l-lg">
                                    Previous
                                </a>
                            @endif

                            @foreach($discussions->getUrlRange(1, $discussions->lastPage()) as $page => $url)
                                @if($page == $discussions->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if($discussions->hasMorePages())
                                <a href="{{ $discussions->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-r-lg">
                                    Next
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 cursor-not-allowed rounded-r-lg">
                                    Next
                                </span>
                            @endif
                        </nav>
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No discussions yet</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Get started by creating a new discussion.</p>
                    @auth
                        <div class="mt-6">
                            <a href="{{ route('discussions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 transition">
                                New Discussion
                            </a>
                        </div>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
