@extends('layouts.forum')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Discussion</h1>
        </div>

        <form action="{{ route('discussions.update', $discussion) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PATCH')

            <!-- Channel -->
            <div>
                <label for="channel_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Channel</label>
                <select name="channel_id" id="channel_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select a channel</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id }}" {{ (old('channel_id', $discussion->channel_id) == $channel->id) ? 'selected' : '' }}>
                            {{ $channel->title }}
                        </option>
                    @endforeach
                </select>
                @error('channel_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $discussion->title) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div x-data="{
                tab: 'write',
                preview: '',
                insertMarkdown(before, after, placeholder) {
                    const textarea = this.$refs.contentTextarea;
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>

                <!-- Tabs -->
                <div class="flex border-b border-gray-200 dark:border-gray-700">
                    <button type="button" @click="tab = 'write'" :class="tab === 'write' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-4 py-2 border-b-2 font-medium text-sm">
                        Write
                    </button>
                    <button type="button" @click="tab = 'preview'; preview = $refs.contentTextarea.value" :class="tab === 'preview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400'" class="px-4 py-2 border-b-2 font-medium text-sm">
                        Preview
                    </button>
                </div>

                <!-- Write Tab -->
                <div x-show="tab === 'write'" class="mt-2">
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
                        x-ref="contentTextarea"
                        name="content"
                        id="content"
                        rows="10"
                        class="block w-full rounded-b-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >{{ old('content', $discussion->content) }}</textarea>

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Supports Markdown: **bold**, *italic*, `code`, [links](url), lists, and more
                    </p>
                </div>

                <!-- Preview Tab -->
                <div x-show="tab === 'preview'" class="mt-2 p-4 min-h-[250px] border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700">
                    <template x-if="preview.trim() === ''">
                        <p class="text-gray-500 dark:text-gray-400 italic">Nothing to preview</p>
                    </template>
                    <template x-if="preview.trim() !== ''">
                        <div class="prose prose-indigo dark:prose-invert max-w-none" x-html="window.marked.parse(preview)"></div>
                    </template>
                </div>

                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Images -->
            <div>
                @livewire('components.image-uploader', [
                    'existingImages' => $discussion->images,
                    'maxFiles' => 5,
                    'label' => 'Attach Images (Optional)',
                    'help' => 'Supported: JPG, PNG, GIF, WebP. Max 5MB per image, 5 images total.'
                ])
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('discussions.show', $discussion) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Update Discussion
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
