@extends('layouts.forum')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Discussion</h1>
        </div>

        <form action="{{ route('discussions.update', $discussion) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
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
            <div x-data="{
                files: [],
                previews: [],
                existingImages: {{ $discussion->images->toJson() }},
                removedImages: [],
                handleFiles(event) {
                    const newFiles = Array.from(event.target.files || event.dataTransfer.files);
                    newFiles.forEach(file => {
                        const totalImages = this.existingImages.length - this.removedImages.length + this.files.length;
                        if (totalImages < 5 && file.type.startsWith('image/')) {
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
                removeExistingImage(imageId) {
                    this.removedImages.push(imageId);
                },
                isRemoved(imageId) {
                    return this.removedImages.includes(imageId);
                },
                updateFileInput() {
                    const dt = new DataTransfer();
                    this.files.forEach(file => dt.items.add(file));
                    this.$refs.fileInput.files = dt.files;
                }
            }">
                <!-- Hidden inputs for removed images -->
                <template x-for="imageId in removedImages" :key="imageId">
                    <input type="hidden" name="removed_images[]" :value="imageId">
                </template>

                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Attach Images (Optional)
                </label>

                <!-- Existing Images -->
                <div x-show="existingImages.length > 0" class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Existing Images</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="image in existingImages" :key="image.id">
                            <div x-show="!isRemoved(image.id)" class="relative group">
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                                    <img
                                        :src="`/storage/${image.path}`"
                                        :alt="image.alt_text || 'Image'"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="removeExistingImage(image.id)"
                                    class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

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
                            Supported: JPG, PNG, GIF, WebP. Max 5MB per image, 5 images total.
                        </p>
                    </div>
                </div>

                @error('images.*')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <!-- Preview New Images -->
                <div x-show="previews.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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
                <p x-show="files.length > 0 || existingImages.length > 0" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <span x-text="(existingImages.length - removedImages.length + files.length)"></span> / 5 images
                    <span x-show="(existingImages.length - removedImages.length + files.length) >= 5" class="text-orange-600 dark:text-orange-400">(Maximum reached)</span>
                </p>
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
