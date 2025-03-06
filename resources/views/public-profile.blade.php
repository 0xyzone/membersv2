<!DOCTYPE html>
<html class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $user->username }} - Esports Profile</title>

    <!-- Primary Meta Tags -->
    <title>{{ $user->username }} - Esports Profile | YourPlatformName</title>
    <meta name="title" content="{{ $user->username }} - Esports Profile | YourPlatformName">
    <meta name="description" content="View {{ $user->username }}'s esports profile on YourPlatformName. Explore game stats, team affiliations, and social connections of this {{ $user->is_active ? 'active' : 'inactive' }} player.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $user->username }} - Esports Profile | YourPlatformName">
    <meta property="og:description" content="View {{ $user->username }}'s esports profile on YourPlatformName. Explore game stats, team affiliations, and social connections of this {{ $user->is_active ? 'active' : 'inactive' }} player.">
    <meta property="og:image" content="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('images/esports-default-avatar.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $user->username }} - Esports Profile | YourPlatformName">
    <meta property="twitter:description" content="View {{ $user->username }}'s esports profile on YourPlatformName. Explore game stats, team affiliations, and social connections of this {{ $user->is_active ? 'active' : 'inactive' }} player.">
    <meta property="twitter:image" content="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('images/esports-default-avatar.png') }}">

    <!-- Additional Meta Tags -->
    <meta name="author" content="{{ $user->username }}">
    <meta name="keywords" content="esports, gaming profile, {{ $user->username }}, {{ implode(', ', $user->userGameInfos->pluck('game.name')->toArray()) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 to-black min-h-screen">
    <div class="lg:container mx-auto px-4 py-4 sm:py-8">
        <!-- Changed to regular container -->
        <!-- Profile Header -->
        <div class="mx-auto bg-slate-800 rounded-xl shadow-2xl overflow-hidden lg:max-w-6xl">
            <!-- Added max-width constraint -->
            <!-- Banner Section -->
            <div class="h-32 sm:h-48 bg-gradient-to-r from-purple-600 to-blue-600 relative">
                <div class="absolute -bottom-8 sm:-bottom-16 left-4 sm:left-6">
                    <img src="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('images/esports-default-avatar.png') }}" class="w-20 h-20 sm:w-32 sm:h-32 rounded-full border-4 border-slate-800 shadow-lg bg-slate-900"> <!-- Increased mobile avatar size -->
                </div>
            </div>

            <!-- Profile Content -->
            <div class="pt-12 sm:pt-20 px-4 sm:px-6 pb-4 sm:pb-6">
                <!-- Basic Info -->
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                    <div class="order-2 sm:order-1">
                        <h1 class="text-3xl font-bold text-white mb-2">
                            <!-- Unified text size -->
                            {{ $user->username }}
                            @if($user->is_verified)
                            <span class="ml-2 text-blue-400" title="Verified Account">
                                <svg class="w-6 h-6 inline" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23 12l-2.44-2.78.34-3.68-3.61-.82-1.89-3.18L12 3 8.6 1.54 6.71 4.72l-3.61.81.34 3.68L1 12l2.44 2.78-.34 3.69 3.61.82 1.89 3.18L12 21l3.4 1.46 1.89-3.18 3.61-.82-.34-3.68L23 12zM9.38 16.01L7 13.61l1.41-1.41 1.41 1.41 4.24-4.24 1.41 1.41-5.66 5.66z" />
                                </svg>
                            </span>
                            @endif
                        </h1>
                        <p class="text-base text-slate-400">{{ $user->name }}</p> <!-- Increased text size -->
                    </div>
                    <div class="order-1 sm:order-2 text-right w-full sm:w-auto">
                        <div class="text-sm text-slate-400">Member since {{ $user->created_at->format('M Y') }}</div> <!-- Increased text size -->
                    </div>
                </div>

                <!-- Personal Stats -->
                <div class="bg-slate-900 p-6 rounded-xl mb-6">
                    <!-- Increased padding -->
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                        <!-- Unified heading size -->
                        <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Player Profile
                    </h2>
                    <div class="grid grid-cols-1 gap-4 text-base text-slate-300">
                        <!-- Increased text size and spacing -->
                        @if($user->gender)
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ ucfirst($user->gender) }}
                        </div>
                        @endif

                        @if($user->date_of_birth)
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($user->date_of_birth)->age }} years old
                        </div>
                        @endif

                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $user->is_active ? 'Active' : 'Inactive' }} Player
                        </div>
                    </div>
                </div>

                <!-- Game Stats Section -->
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 my-6">
                    <!-- Simplified grid -->
                    @foreach($user->userGameInfos as $gameInfo)
                    <div class="bg-slate-900 p-4 rounded-xl">
                        <div class="flex items-center gap-4">
                            @if($gameInfo->game->image_path)
                            <img src="{{ asset('storage/' . $gameInfo->game->image_path) }}" alt="{{ $gameInfo->game->name }} Logo" class="w-16 h-16 rounded-xl bg-slate-800 p-2">
                            @endif
                            <div>
                                <div class="text-lg font-bold text-purple-400">{{ $gameInfo->ingame_name }}</div>
                                <div class="text-sm text-slate-400">{{ $gameInfo->game->name }}</div>
                                @if($gameInfo->ingame_id)
                                <div class="text-sm text-slate-400 mt-1">
                                    ID: <span class="font-mono text-amber-400">{{ $gameInfo->ingame_id }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($user->ownedTeams->isNotEmpty() || $user->teams->isNotEmpty())
                <div class="bg-slate-900 p-6 rounded-xl mt-6">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Team Affiliations
                    </h2>

                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Changed to vertical stack -->
                        @foreach($user->ownedTeams as $team)
                        <div class="bg-slate-800 p-4 rounded-xl">
                            <div class="flex items-center gap-4">
                                @if($team->team_logo_image_path)
                                <img src="{{ asset('storage/' . $team->team_logo_image_path) }}" alt="{{ $team->name }} Logo" class="w-16 h-16 rounded-xl bg-slate-700 p-2">
                                @endif
                                <div>
                                    <h3 class="text-lg font-bold text-white">{{ $team->name }}</h3>
                                    <div class="text-sm text-slate-400">
                                        <span class="text-amber-400">Owner</span> • {{ $team->game->name }}
                                        @if($team->ingame_team_id)
                                        <div class="mt-1">ID: {{ $team->ingame_team_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Social Connections -->
                <div class="bg-slate-900 p-6 rounded-xl mt-6">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Social Connections
                    </h2>
                    <div class="space-y-4">
                        <!-- Vertical stack for mobile -->
                        @foreach($user->socials as $social)
                        <a href="{{ $social->link }}" target="_blank" class="flex items-center p-4 bg-slate-800 rounded-xl hover:bg-slate-700 transition-colors">
                            <span class="w-8 h-8 flex items-center justify-center">
                                <x-dynamic-component :component="'bi-' . $social->type" class="w-6 h-6 {{ match(strtolower($social->type)) {
                                      'facebook' => 'text-[#1877F2]',
                                      'x' => 'text-[#1DA1F2]',
                                      'instagram' => 'text-[#E1306C]',
                                      'twitch' => 'text-[#9146FF]',
                                      'discord' => 'text-[#5865F2]',
                                      default => 'text-slate-400'
                                  } }}" />
                            </span>
                            <span class="ml-4 text-lg font-medium text-slate-300">
                                {{ $social->username }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
