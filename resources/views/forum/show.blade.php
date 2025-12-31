@extends('layouts.forum')

@section('title', $discussion->title)
@section('description', Str::limit(strip_tags(markdown($discussion->content)), 155))
@section('og_type', 'article')
@section('og_title', $discussion->title)
@section('og_description', Str::limit(strip_tags(markdown($discussion->content)), 200))
@section('og_url', route('discussions.show', $discussion))
@section('canonical', route('discussions.show', $discussion))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumbs -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('forum.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Forum
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('channel.show', $discussion->channel) }}" class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 md:ml-2">
                        {{ $discussion->channel->title }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-500 md:ml-2 truncate max-w-xs">{{ Str::limit($discussion->title, 30) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Discussion -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <!-- Discussion Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white break-words">{{ $discussion->title }}</h1>
                            <div class="mt-3 flex flex-wrap items-center gap-4 text-sm">
                                <a href="{{ route('channel.show', $discussion->channel) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    {{ $discussion->channel->title }}
                                </a>
                                <span class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ number_format($discussion->views) }} views
                                </span>
                                <span class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $discussion->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        @auth
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <!-- Watch Button -->
                                <form action="{{ route('discussions.watch', $discussion) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border {{ $isWatching ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }} rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ $isWatching ? 'Watching' : 'Watch' }}
                                    </button>
                                </form>

                                <!-- Action Dropdown -->
                                @if(auth()->check() && (auth()->id() === $discussion->user_id || auth()->user()->admin))
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                            </svg>
                                        </button>

                                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                            @can('update', $discussion)
                                                <a href="{{ route('discussions.edit', $discussion) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-t-lg">
                                                    Edit Discussion
                                                </a>
                                            @endcan
                                            @can('delete', $discussion)
                                                <form action="{{ route('discussions.destroy', $discussion) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-lg">
                                                        Delete Discussion
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Discussion Content -->
                <div class="px-6 py-6">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow-md">
                                {{ $discussion->user->initials() }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $discussion->user->name }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <svg class="w-3 h-3 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    {{ number_format($discussion->user->points) }} points
                                </span>
                            </div>
                            <div class="prose prose-indigo dark:prose-invert max-w-none">
                                {!! markdown($discussion->content) !!}
                            </div>

                            <!-- Discussion Images -->
                            @if($discussion->images->count() > 0)
                                <div class="mt-6">
                                    @livewire('components.image-gallery', [
                                        'images' => $discussion->images,
                                        'columns' => 4
                                    ])
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    {{ $discussion->replies->count() }} {{ Str::plural('Reply', $discussion->replies->count()) }}
                </h2>

                <div class="space-y-4">
                    @foreach($discussion->replies as $reply)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border {{ $reply->best_answer ? 'border-green-500 dark:border-green-600' : 'border-gray-200 dark:border-gray-700' }}">
                            @if($reply->best_answer)
                                <div class="px-6 py-2 bg-green-50 dark:bg-green-900/20 border-b border-green-200 dark:border-green-800 rounded-t-xl">
                                    <span class="inline-flex items-center text-sm font-medium text-green-800 dark:text-green-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Best Answer
                                    </span>
                                </div>
                            @endif

                            <div class="px-6 py-4">
                                <div class="flex gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-500 to-gray-700 flex items-center justify-center text-white font-semibold text-sm shadow">
                                            {{ $reply->user->initials() }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($reply->user->points) }} points</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">•</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>

                                            @auth
                                                <div class="flex items-center gap-2">
                                                    <!-- Like Button -->
                                                    <form action="{{ route('replies.like', $reply) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 text-sm font-medium rounded-lg {{ $reply->likes->where('user_id', auth()->id())->count() > 0 ? 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                                                            <svg class="w-4 h-4 mr-1" fill="{{ $reply->likes->where('user_id', auth()->id())->count() > 0 ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                            </svg>
                                                            {{ $reply->likes->count() }}
                                                        </button>
                                                    </form>

                                                    <!-- Mark Best Answer -->
                                                    @if(!$reply->best_answer && auth()->id() === $discussion->user_id)
                                                        <form action="{{ route('replies.best-answer', $reply) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-xs px-2 py-1 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg font-medium transition">
                                                                Mark as best
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @can('delete', $reply)
                                                        <form action="{{ route('replies.destroy', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-xs px-2 py-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg font-medium transition">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            @endauth
                                        </div>
                                        <div class="prose prose-sm prose-indigo dark:prose-invert max-w-none">
                                            {!! markdown($reply->content) !!}
                                        </div>

                                        <!-- Reply Images -->
                                        @if($reply->images->count() > 0)
                                            <div class="mt-4">
                                                @livewire('components.image-gallery', [
                                                    'images' => $reply->images,
                                                    'columns' => 3
                                                ])
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Form -->
            @auth
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Post a Reply</h3>
                    <form action="{{ route('replies.store', $discussion) }}" method="POST" enctype="multipart/form-data" x-data="{
                        tab: 'write',
                        preview: '',
                        files: [],
                        previews: [],
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
                        },
                        handleFiles(event) {
                            const newFiles = Array.from(event.target.files || event.dataTransfer.files);
                            newFiles.forEach(file => {
                                if (this.files.length < 3 && file.type.startsWith('image/')) {
                                    this.files.push(file);
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.previews.push({
                                            url: e.target.result,
                                            name: file.name,
                                            size: (file.size / 1024).toFixed(1) + ' KB'
                                        });
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                            this.updateFileInput();
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            this.previews.splice(index, 1);
                            this.updateFileInput();
                        },
                        updateFileInput() {
                            const dt = new DataTransfer();
                            this.files.forEach(file => dt.items.add(file));
                            this.$refs.fileInput.files = dt.files;
                        }
                    }">
                        @csrf

                        <!-- Tabs -->
                        <div class="flex border-b border-gray-200 dark:border-gray-700 mb-2">
                            <button type="button" @click="tab = 'write'" :class="tab === 'write' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-4 py-2 border-b-2 font-medium text-sm transition">
                                Write
                            </button>
                            <button type="button" @click="tab = 'preview'; preview = $refs.replyTextarea.value" :class="tab === 'preview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-4 py-2 border-b-2 font-medium text-sm transition">
                                Preview
                            </button>
                        </div>

                        <!-- Write Tab -->
                        <div x-show="tab === 'write'">
                            <!-- Markdown Toolbar -->
                            <div class="flex gap-1 p-2 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-t-lg">
                                <button type="button" @click="insertMarkdown('**', '**', 'bold text')" class="px-2 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition" title="Bold">
                                    <strong>B</strong>
                                </button>
                                <button type="button" @click="insertMarkdown('*', '*', 'italic text')" class="px-2 py-1 text-sm italic text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition" title="Italic">
                                    <em>I</em>
                                </button>
                                <button type="button" @click="insertMarkdown('[', '](url)', 'link text')" class="px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition" title="Link">
                                    🔗
                                </button>
                                <button type="button" @click="insertMarkdown('`', '`', 'code')" class="px-2 py-1 text-sm font-mono text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition" title="Inline Code">
                                    &lt;/&gt;
                                </button>
                                <button type="button" @click="insertMarkdown('\n```\n', '\n```\n', 'code block')" class="px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition" title="Code Block">
                                    { }
                                </button>
                                <button type="button" @click="insertMarkdown('- ', '', 'list item')" class="px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition" title="Bullet List">
                                    ≡
                                </button>
                            </div>

                            <textarea
                                x-ref="replyTextarea"
                                name="content"
                                rows="6"
                                class="w-full rounded-b-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                        <div x-show="tab === 'preview'" class="p-4 min-h-[180px] border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900">
                            <template x-if="preview.trim() === ''">
                                <p class="text-gray-500 dark:text-gray-400 italic">Nothing to preview</p>
                            </template>
                            <template x-if="preview.trim() !== ''">
                                <div class="prose prose-sm prose-indigo dark:prose-invert max-w-none" x-html="window.marked.parse(preview)"></div>
                            </template>
                        </div>

                        <!-- Images -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Attach Images (Optional)
                            </label>

                            <!-- Upload Area -->
                            <div
                                @dragover.prevent="$el.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/10')"
                                @dragleave.prevent="$el.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/10')"
                                @drop.prevent="
                                    $el.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/10');
                                    handleFiles($event);
                                "
                                class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 transition-colors hover:border-gray-400 dark:hover:border-gray-500"
                            >
                                <input
                                    x-ref="fileInput"
                                    type="file"
                                    name="images[]"
                                    multiple
                                    accept="image/*"
                                    @change="handleFiles($event)"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                />

                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 cursor-pointer">
                                            Click to upload
                                        </span>
                                        or drag and drop
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                        Supported: JPG, PNG, GIF, WebP. Max 5MB per image, 3 images total.
                                    </p>
                                </div>
                            </div>

                            @error('images.*')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <!-- Preview Images -->
                            <div x-show="previews.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                                <template x-for="(preview, index) in previews" :key="index">
                                    <div class="relative group">
                                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                            <img
                                                :src="preview.url"
                                                :alt="preview.name"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeFile(index)"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                        <div class="absolute bottom-2 left-2 bg-black/50 text-white text-xs px-2 py-1 rounded" x-text="preview.size"></div>
                                    </div>
                                </template>
                            </div>

                            <!-- File count -->
                            <p x-show="files.length > 0" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span x-text="files.length"></span> / 3 images
                                <span x-show="files.length >= 3" class="text-orange-600 dark:text-orange-400">(Maximum reached)</span>
                            </p>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 transition">
                                Post Reply
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Join the discussion</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">Log in</a> or
                        <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">register</a> to post a reply.
                    </p>
                </div>
            @endauth
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Author Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Author</h3>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow-md">
                        {{ $discussion->user->initials() }}
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $discussion->user->name }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($discussion->user->points) }} points</div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Discussions</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $discussion->user->discussions->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Replies</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $discussion->user->replies->count() }}</span>
                    </div>
                </div>

                @if($discussion->user->badges->count() > 0)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Badges</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($discussion->user->badges->sortByDesc('points_required')->take(6) as $badge)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-{{ $badge->color }}-100 dark:bg-{{ $badge->color }}-900/20 text-{{ $badge->color }}-800 dark:text-{{ $badge->color }}-200" title="{{ $badge->description }}">
                                    {{ $badge->icon }} {{ $badge->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Discussion Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Discussion Stats</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Views</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($discussion->views) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Replies</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $discussion->replies->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Watchers</dt>
                        <dd class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">{{ $discussion->watchers->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600 dark:text-gray-400">Created</dt>
                        <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $discussion->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
