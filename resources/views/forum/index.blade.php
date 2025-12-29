@extends('layouts.forum')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar - Channels -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Channels</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('forum.index') }}" class="block px-3 py-2 rounded {{ !request()->route('channel') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            All Discussions
                        </a>
                    </li>
                    @foreach($channels as $channel)
                        <li>
                            <a href="{{ route('channel.show', $channel) }}" class="block px-3 py-2 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ $channel->title }}
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $channel->discussions_count }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                @auth
                    @if(auth()->user()->admin)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.channels.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                Manage Channels
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Main Content - Discussions -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Discussions</h1>
                </div>

                @if($discussions->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($discussions as $discussion)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('discussions.show', $discussion) }}" class="block">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ $discussion->title }}
                                            </h3>
                                        </a>
                                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $discussion->user->name }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                {{ $discussion->channel->title }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ $discussion->views }} views
                                            </span>
                                            <span>{{ $discussion->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex items-center gap-2">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $discussion->replies_count }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">replies</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $discussions->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No discussions</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new discussion.</p>
                        @auth
                            <div class="mt-6">
                                <a href="{{ route('discussions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    New Discussion
                                </a>
                            </div>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
