@extends('layouts.forum')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Discussion -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <!-- Discussion Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $discussion->title }}</h1>
                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('channel.show', $discussion->channel) }}" class="flex items-center gap-1 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            {{ $discussion->channel->title }}
                        </a>
                        <span>{{ $discussion->views }} views</span>
                        <span>{{ $discussion->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @auth
                    <div class="flex items-center gap-2">
                        <!-- Watch Button -->
                        <form action="{{ route('discussions.watch', $discussion) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium {{ $isWatching ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ $isWatching ? 'Watching' : 'Watch' }}
                            </button>
                        </form>

                        @can('update', $discussion)
                            <a href="{{ route('discussions.edit', $discussion) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $discussion)
                            <form action="{{ route('discussions.destroy', $discussion) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 dark:border-red-600 rounded-md text-sm font-medium bg-white dark:bg-gray-700 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                @endauth
            </div>
        </div>

        <!-- Discussion Content -->
        <div class="px-6 py-6">
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                        {{ $discussion->user->initials() }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $discussion->user->name }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $discussion->user->points }} points</span>
                    </div>
                    <div class="mt-4 prose prose-indigo dark:prose-invert max-w-none">
                        {!! markdown($discussion->content) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            {{ $discussion->replies->count() }} {{ Str::plural('Reply', $discussion->replies->count()) }}
        </h2>

        <div class="space-y-4">
            @foreach($discussion->replies as $reply)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow {{ $reply->best_answer ? 'ring-2 ring-green-500 dark:ring-green-600' : '' }}">
                    @if($reply->best_answer)
                        <div class="px-6 py-2 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800">
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">✓ Best Answer</span>
                        </div>
                    @endif

                    <div class="px-6 py-4">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $reply->user->initials() }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $reply->user->points }} points</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                    </div>

                                    @auth
                                        <div class="flex items-center gap-2">
                                            <!-- Like Button -->
                                            <form action="{{ route('replies.like', $reply) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-2 py-1 text-sm {{ $reply->likes->where('user_id', auth()->id())->count() > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400">
                                                    <svg class="w-4 h-4 mr-1" fill="{{ $reply->likes->where('user_id', auth()->id())->count() > 0 ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                    {{ $reply->likes->count() }}
                                                </button>
                                            </form>

                                            <!-- Mark Best Answer -->
                                            @if(!$reply->best_answer && auth()->id() === $discussion->user_id)
                                                <form action="{{ route('replies.best-answer', $reply) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300">
                                                        Mark as best answer
                                                    </button>
                                                </form>
                                            @endif

                                            @can('update', $reply)
                                                <button class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Edit</button>
                                            @endcan

                                            @can('delete', $reply)
                                                <form action="{{ route('replies.destroy', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endauth
                                </div>
                                <div class="mt-2 prose prose-sm prose-indigo dark:prose-invert max-w-none">
                                    {!! markdown($reply->content) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Reply Form -->
    @auth
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Post a Reply</h3>
            <form action="{{ route('replies.store', $discussion) }}" method="POST" x-data="{
                tab: 'write',
                preview: '',
                insertMarkdown(before, after, placeholder) {
                    const textarea = this.$refs.replyTextarea;
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const selectedText = textarea.value.substring(start, end);
                    const textToInsert = selectedText || placeholder;
                    const newText = textarea.value.substring(0, start) + before + textToInsert + after + textarea.value.substring(end);

                    textarea.value = newText;
                    textarea.focus();
                    textarea.setSelectionRange(start + before.length, start + before.length + textToInsert.length);
                }
            }">
                @csrf

                <!-- Tabs -->
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-2">
                    <button type="button" @click="tab = 'write'" :class="tab === 'write' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-4 py-2 border-b-2 font-medium text-sm">
                        Write
                    </button>
                    <button type="button" @click="tab = 'preview'; preview = $refs.replyTextarea.value" :class="tab === 'preview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-4 py-2 border-b-2 font-medium text-sm">
                        Preview
                    </button>
                </div>

                <!-- Write Tab -->
                <div x-show="tab === 'write'">
                    <!-- Markdown Toolbar -->
                    <div class="flex gap-1 p-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-t-md">
                        <button type="button" @click="insertMarkdown('**', '**', 'bold text')" class="px-2 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Bold">
                            <strong>B</strong>
                        </button>
                        <button type="button" @click="insertMarkdown('*', '*', 'italic text')" class="px-2 py-1 text-sm italic text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Italic">
                            <em>I</em>
                        </button>
                        <button type="button" @click="insertMarkdown('[', '](url)', 'link text')" class="px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Link">
                            🔗
                        </button>
                        <button type="button" @click="insertMarkdown('`', '`', 'code')" class="px-2 py-1 text-sm font-mono text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Inline Code">
                            &lt;/&gt;
                        </button>
                        <button type="button" @click="insertMarkdown('\n```\n', '\n```\n', 'code block')" class="px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Code Block">
                            { }
                        </button>
                        <button type="button" @click="insertMarkdown('- ', '', 'list item')" class="px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Bullet List">
                            ≡
                        </button>
                    </div>

                    <textarea
                        x-ref="replyTextarea"
                        name="content"
                        rows="4"
                        class="w-full rounded-b-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Write your reply... (Markdown supported)"
                        required
                    >{{ old('content') }}</textarea>

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Supports Markdown: **bold**, *italic*, `code`, [links](url), lists, and more
                    </p>

                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Tab -->
                <div x-show="tab === 'preview'" class="p-4 min-h-[120px] border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700">
                    <template x-if="preview.trim() === ''">
                        <p class="text-gray-500 dark:text-gray-400 italic">Nothing to preview</p>
                    </template>
                    <template x-if="preview.trim() !== ''">
                        <div class="prose prose-sm prose-indigo dark:prose-invert max-w-none" x-html="window.marked.parse(preview)"></div>
                    </template>
                </div>

                <div class="mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Post Reply
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
            <p class="text-gray-600 dark:text-gray-400">
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">Log in</a> or
                <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">register</a> to post a reply.
            </p>
        </div>
    @endauth
</div>
@endsection
