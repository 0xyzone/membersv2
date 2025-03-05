<!DOCTYPE html>
<html class="dark">
<head>
    <title>{{ $user->username }} - Esports Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-900 to-black min-h-screen">
    <div class="container mx-auto px-4 py-4 sm:py-8">
        <!-- Profile Header -->
        <div class="mx-auto bg-slate-800 rounded-xl shadow-2xl overflow-hidden">
            <!-- Banner Section -->
            <div class="h-32 sm:h-48 bg-gradient-to-r from-purple-600 to-blue-600 relative">
                <div class="absolute -bottom-8 sm:-bottom-16 left-4 sm:left-6">
                    <img src="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('images/esports-default-avatar.png') }}" class="w-16 h-16 sm:w-32 sm:h-32 rounded-full border-4 border-slate-800 shadow-lg bg-slate-900">
                </div>
            </div>

            <!-- Profile Content -->
            <div class="pt-12 sm:pt-20 px-4 sm:px-6 pb-4 sm:pb-6">
                <!-- Basic Info -->
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                    <div class="order-2 sm:order-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1">
                            {{ $user->username }}
                            @if($user->is_verified)
                            <span class="ml-2 text-blue-400" title="Verified Account">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 inline" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23 12l-2.44-2.78.34-3.68-3.61-.82-1.89-3.18L12 3 8.6 1.54 6.71 4.72l-3.61.81.34 3.68L1 12l2.44 2.78-.34 3.69 3.61.82 1.89 3.18L12 21l3.4 1.46 1.89-3.18 3.61-.82-.34-3.68L23 12zM9.38 16.01L7 13.61l1.41-1.41 1.41 1.41 4.24-4.24 1.41 1.41-5.66 5.66z" />
                                </svg>
                            </span>
                            @endif
                        </h1>
                        <p class="text-sm sm:text-base text-slate-400">{{ $user->name }}</p>
                    </div>
                    <div class="order-1 sm:order-2 text-right w-full sm:w-auto">
                        <div class="text-xs sm:text-sm text-slate-400">Member since {{ $user->created_at->format('M Y') }}</div>
                    </div>
                </div>

                <!-- Personal Stats -->
                <div class="bg-slate-900 p-4 sm:p-6 rounded-xl mb-4">
                    <h2 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Player Profile
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm sm:text-base text-slate-300">
                        @if($user->gender)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ ucfirst($user->gender) }}
                        </div>
                        @endif

                        @if($user->date_of_birth)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($user->date_of_birth)->age }} years old
                        </div>
                        @endif

                        <div class="flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $user->is_active ? 'Active' : 'Inactive' }} Player
                        </div>
                    </div>
                </div>

                <!-- Game Stats Section -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 my-4">
                    @foreach($user->userGameInfos as $gameInfo)
                    <div class="bg-slate-900 p-3 sm:p-4 rounded-lg hover:transform sm:hover:scale-105 transition-all duration-300">
                        <div class="flex flex-col sm:flex-row items-center gap-3">
                            @if($gameInfo->game->image_path)
                            <img src="{{ asset('storage/' . $gameInfo->game->image_path) }}" alt="{{ $gameInfo->game->name }} Logo" class="w-12 h-12 sm:w-16 sm:h-16 object-contain rounded-lg bg-slate-800 p-1 sm:p-2">
                            @endif
                            <div class="text-center sm:text-left">
                                <div class="text-xs sm:text-sm text-slate-400 mb-1">{{ $gameInfo->game->name }}</div>
                                <div class="text-base sm:text-xl font-bold text-purple-400">{{ $gameInfo->ingame_name }}</div>
                                @if($gameInfo->ingame_id)
                                <div class="text-xs sm:text-sm text-slate-400 mt-1">
                                    ID: <span class="font-mono text-amber-400">{{ $gameInfo->ingame_id }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($user->ownedTeams->isNotEmpty() || $user->teams->isNotEmpty())
                <div class="bg-slate-900 p-4 sm:p-6 rounded-xl mt-4">
                    <h2 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Team Affiliations
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Owned Teams -->
                        @foreach($user->ownedTeams as $team)
                        <div class="bg-slate-800 p-4 rounded-lg hover:transform sm:hover:scale-105 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-3">
                                @if($team->team_logo_image_path)
                                <img src="{{ asset('storage/' . $team->team_logo_image_path) }}" alt="{{ $team->name }} Logo" class="w-12 h-12 rounded-full bg-slate-700 p-1">
                                @else
                                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <h3 class="font-bold text-white">{{ $team->name }}</h3>
                                    <p class="text-sm text-slate-400">{{ $team->game->name }}
                                        @if ($team->ingame_team_id != null)
                                        <span class="mx-2">
                                            •</span>
                                        <span class="text-sm">ID: {{ $team->ingame_team_id }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm text-slate-300">
                                <div class="flex items-center mb-1">
                                    <span class="text-amber-400">Owner</span>
                                </div>
                                <div class="text-xs text-slate-400">Members: {{ $team->members->count() }}</div>
                            </div>
                        </div>
                        @endforeach

                        <!-- Member Teams -->
                        @foreach($user->teams as $team)
                        <div class="bg-slate-800 p-4 rounded-lg hover:transform sm:hover:scale-105 transition-all duration-300">
                            <div class="flex items-center gap-3 mb-3">
                                @if($team->team_logo_image_path)
                                <img src="{{ asset('storage/' . $team->team_logo_image_path) }}" alt="{{ $team->name }} Logo" class="w-12 h-12 rounded-full bg-slate-700 p-1">
                                @else
                                <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <h3 class="font-bold text-white">{{ $team->name }}</h3>
                                    <p class="text-sm text-slate-400">{{ $team->game->name }}
                                        @if ($team->ingame_team_id != null)
                                        <span class="mx-2">
                                            •</span>
                                        <span class="text-sm">ID: {{ $team->ingame_team_id }}</span>
                                        @endif</p>
                                </div>
                            </div>
                            <div class="text-sm text-slate-300">
                                <div class="flex items-center mb-1">
                                    <span class="text-purple-400">{{ $team->pivot->role ?? 'Member' }}</span>
                                </div>
                                <div class="text-xs text-slate-400">Members: {{ $team->members->count() }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Social Connections -->
                <div class="bg-slate-900 p-4 sm:p-6 rounded-xl mt-4">
                    <h2 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Social Connections
                    </h2>
                    <div class="grid grid-cols-1 lg:grid-cols-4">
                        @foreach($user->socials as $social)
                        <a href="{{ $social->link }}" target="_blank" class="flex items-center p-2 sm:p-3 bg-slate-800 rounded-lg hover:bg-slate-700 transition-colors">
                            <span class="w-6 h-6 sm:w-8 sm:h-8 flex items-center justify-center">
                                <x-dynamic-component :component="'bi-' . $social->type" class="w-4 h-4 sm:w-6 sm:h-6 {{ match(strtolower($social->type)) {
                                        'facebook' => 'text-[#1877F2]',
                                        'x' => 'text-[#1DA1F2]',
                                        'instagram' => 'text-[#E1306C]',
                                        'twitch' => 'text-[#9146FF]',
                                        'discord' => 'text-[#5865F2]',
                                        default => 'text-slate-400'
                                    } }}" />
                            </span>
                            <span class="ml-2 sm:ml-3 text-sm sm:text-base font-medium text-slate-300">
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
