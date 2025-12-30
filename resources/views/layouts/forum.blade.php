<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic Title --}}
    <title>@yield('title', 'Forum') - {{ config('app.name', 'DevForum') }}</title>

    {{-- Meta Description --}}
    <meta name="description" content="@yield('description', 'Join our developer community. Ask questions, share knowledge, and connect with developers.')">

    {{-- Open Graph Tags --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'DevForum'))">
    <meta property="og:description" content="@yield('og_description', 'Join our developer community. Ask questions, share knowledge, and connect with developers.')">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'DevForum') }}">
    @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
    @endif

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', config('app.name', 'DevForum'))">
    <meta name="twitter:description" content="@yield('og_description', 'Join our developer community. Ask questions, share knowledge, and connect with developers.')">
    @hasSection('og_image')
    <meta name="twitter:image" content="@yield('og_image')">
    @endif

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ request()->url() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center gap-8">
                        <!-- Logo -->
                        <a href="{{ route('forum.index') }}" class="text-xl font-bold text-gray-900 dark:text-white">
                            DevForum
                        </a>

                        <!-- Navigation Links -->
                        <div class="flex gap-6">
                            <a href="{{ route('forum.index') }}" class="text-sm font-medium {{ request()->routeIs('forum.*') || request()->routeIs('channel.*') || request()->routeIs('discussions.*') ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                Forum
                            </a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                                    Dashboard
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md mx-8 hidden md:block">
                        <form action="{{ route('search') }}" method="GET" class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text"
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Search discussions..."
                                   class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </form>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('discussions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 transition">
                                New Discussion
                            </a>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                    {{ auth()->user()->points }} points
                                </span>
                                <a href="{{ route('profile.edit') }}" class="flex items-center justify-center w-10 h-10 bg-indigo-600 text-white rounded-full font-semibold text-sm hover:bg-indigo-700 transition">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </a>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Log in</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 transition">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-8">
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
