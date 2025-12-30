<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $images = [];
    public $existingImages = [];
    public $removedImages = [];
    public $maxFiles = 5;
    public $maxSize = 5120; // KB
    public $label = 'Upload Images';
    public $help = 'Supported: JPG, PNG, GIF, WebP. Max 5MB per image.';

    public function mount($existingImages = [], $maxFiles = 5)
    {
        $this->existingImages = is_array($existingImages) ? $existingImages : $existingImages->all();
        $this->maxFiles = $maxFiles;
    }

    public function updatedImages()
    {
        $this->validate([
            'images.*' => 'image|max:' . $this->maxSize,
        ]);
    }

    public function removeImage($index)
    {
        array_splice($this->images, $index, 1);
    }

    public function removeExistingImage($imageId)
    {
        $this->removedImages[] = $imageId;
        $this->existingImages = array_filter(
            $this->existingImages,
            fn($img) => $img->id !== $imageId
        );
        $this->existingImages = array_values($this->existingImages);
    }
}; ?>

<div class="space-y-4">
    <!-- Hidden inputs for removed images -->
    @foreach($removedImages as $removedId)
        <input type="hidden" name="removed_images[]" value="{{ $removedId }}">
    @endforeach

    <!-- Label -->
    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <!-- Upload Area -->
    <div
        x-data="{
            isDragging: false,
            handleDrop(e) {
                this.isDragging = false;
                const files = Array.from(e.dataTransfer.files);
                @this.upload('images', files);
            }
        }"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
        :class="{ 'border-primary-500 bg-primary-50 dark:bg-primary-900/10': isDragging }"
        class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 transition-colors hover:border-gray-400 dark:hover:border-gray-500"
    >
        <input
            type="file"
            wire:model="images"
            multiple
            accept="image/*"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            id="image-upload-{{ $this->getId() }}"
        />

        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                <label for="image-upload-{{ $this->getId() }}" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 cursor-pointer">
                    Click to upload
                </label>
                or drag and drop
            </p>
            @if($help)
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                    {{ $help }}
                </p>
            @endif
        </div>

        <!-- Upload Progress -->
        <div wire:loading wire:target="images" class="mt-4">
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-primary-600 h-2 rounded-full animate-pulse" style="width: 100%"></div>
            </div>
            <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-2">Uploading...</p>
        </div>
    </div>

    <!-- Validation Errors -->
    @error('images.*')
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <!-- Preview New Images -->
    @if(count($images) > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $index => $image)
                <div class="relative group">
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                        <img
                            src="{{ $image->temporaryUrl() }}"
                            alt="Preview"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <button
                        type="button"
                        wire:click="removeImage({{ $index }})"
                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="absolute bottom-2 left-2 bg-black/50 text-white text-xs px-2 py-1 rounded">
                        {{ number_format($image->getSize() / 1024, 1) }} KB
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Preview Existing Images -->
    @if(count($existingImages) > 0)
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Existing Images</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($existingImages as $image)
                    <div class="relative group">
                        <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
                            <img
                                src="{{ $image->url() }}"
                                alt="{{ $image->alt_text ?? 'Image' }}"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <button
                            type="button"
                            wire:click="removeExistingImage({{ $image->id }})"
                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-700"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="absolute bottom-2 left-2 bg-black/50 text-white text-xs px-2 py-1 rounded">
                            {{ $image->getFormattedSize() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- File count indicator -->
    @if(count($images) > 0 || count($existingImages) > 0)
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ count($images) + count($existingImages) }} / {{ $maxFiles }} images
            @if(count($images) + count($existingImages) >= $maxFiles)
                <span class="text-orange-600 dark:text-orange-400">(Maximum reached)</span>
            @endif
        </p>
    @endif
</div>
