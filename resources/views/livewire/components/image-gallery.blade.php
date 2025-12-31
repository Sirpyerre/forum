<?php

use Livewire\Volt\Component;
use App\Models\Image;

new class extends Component {
    public $images = [];
    public $columns = 4; // Grid columns: 2, 3, 4, 5

    public function mount($images = [], $columns = 4)
    {
        $this->images = $images;
        $this->columns = $columns;
    }
}; ?>

<div
    x-data="{
        showLightbox: false,
        currentIndex: 0,
        totalImages: {{ count($images) }},
        openLightbox(index) {
            this.currentIndex = index;
            this.showLightbox = true;
        },
        closeLightbox() {
            this.showLightbox = false;
        },
        next() {
            this.currentIndex = (this.currentIndex + 1) % this.totalImages;
        },
        previous() {
            this.currentIndex = (this.currentIndex - 1 + this.totalImages) % this.totalImages;
        }
    }"
    @keydown.escape.window="closeLightbox()"
    @keydown.arrow-right.window="showLightbox && next()"
    @keydown.arrow-left.window="showLightbox && previous()"
>
    @if(count($images) > 0)
        <!-- Gallery Grid -->
        <div class="grid gap-4 @if($columns === 2) grid-cols-2 @elseif($columns === 3) grid-cols-2 md:grid-cols-3 @elseif($columns === 5) grid-cols-2 md:grid-cols-3 lg:grid-cols-5 @else grid-cols-2 md:grid-cols-3 lg:grid-cols-4 @endif">
            @foreach($images as $index => $image)
                <div class="relative group cursor-pointer" @click="openLightbox({{ $index }})">
                    <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 transition-transform group-hover:scale-105">
                        <img
                            src="{{ $image->url() }}"
                            alt="{{ $image->alt_text ?? 'Image ' . ($index + 1) }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        />
                    </div>
                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Lightbox Modal -->
        <div
            x-show="showLightbox"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
            @click="closeLightbox()"
        >
            <!-- Close Button -->
            <button
                @click.stop="closeLightbox()"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Previous Button -->
            @if(count($images) > 1)
                <button
                    @click.stop="previous()"
                    class="absolute left-4 text-white hover:text-gray-300 transition-colors z-10"
                >
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            <!-- Image Container -->
            <div class="max-w-7xl max-h-[90vh] w-full h-full flex items-center justify-center" @click.stop>
                @foreach($images as $index => $image)
                    <div x-show="currentIndex === {{ $index }}" class="w-full h-full flex flex-col items-center justify-center">
                        <img
                            src="{{ $image->url() }}"
                            alt="{{ $image->alt_text ?? 'Image ' . ($index + 1) }}"
                            class="max-w-full max-h-full object-contain rounded-lg"
                        />
                        <!-- Image Info -->
                        <div class="mt-4 text-center">
                            @if($image->alt_text)
                                <p class="text-white text-sm mb-2">{{ $image->alt_text }}</p>
                            @endif
                            <p class="text-gray-400 text-xs">
                                {{ $index + 1 }} / {{ count($images) }}
                                @if($image->width && $image->height)
                                    · {{ $image->width }} × {{ $image->height }}
                                @endif
                                · {{ $image->getFormattedSize() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Next Button -->
            @if(count($images) > 1)
                <button
                    @click.stop="next()"
                    class="absolute right-4 text-white hover:text-gray-300 transition-colors z-10"
                >
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @endif

            <!-- Counter (mobile) -->
            @if(count($images) > 1)
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm bg-black/50 px-3 py-1 rounded-full">
                    <span x-text="currentIndex + 1"></span> / {{ count($images) }}
                </div>
            @endif
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No images available</p>
    @endif
</div>
