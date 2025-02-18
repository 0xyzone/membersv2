<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidanta Portal - Champions Arena</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <style>
        @keyframes arenaGlow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .arena-gradient {
            background: linear-gradient(135deg, #1a1b2d, #2d1a2d, #1a2d2d);
            background-size: 400% 400%;
            animation: arenaGlow 15s ease infinite;
        }

        .pixel-border {
            border-image: linear-gradient(45deg, #7c3aed, #4f46e5) 1;
            border-width: 2px;
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }

    </style>
</head>
<body class="bg-stone-950 text-stone-100 font-sans">

    <!-- Navigation -->
    <nav class="arena-gradient px-6 py-4 border-b-2 border-purple-500/30">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <i class="fas fa-gamepad text-purple-400 text-2xl"></i>
                <div>
                    <h1 class="text-xl font-bold">Vidanta Portal</h1>
                    <p class="text-sm text-stone-400">By Vidanta Champions Arena</p>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <span class="text-sm text-purple-300">
                    <i class="fas fa-users mr-1"></i>245K Competitors
                </span>
                <span class="text-sm text-blue-300">
                    <i class="fas fa-trophy mr-1"></i>12K Tournaments
                </span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden min-h-[80vh] flex items-center">
        <div class="arena-gradient absolute inset-0 z-0"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                {{-- <div class="inline-block bg-black/30 backdrop-blur-sm px-6 py-2 rounded-full mb-8">
                    <span class="text-purple-400 font-mono">Season 4: Champions Rise</span>
                </div> --}}
                <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
                    Enter the Arena
                </h1>
                <p class="text-xl text-stone-300 mb-12 max-w-2xl mx-auto">
                    Where elite gamers clash in epic tournaments. Build your legacy, manage teams,
                    and compete in the ultimate esports battleground.
                </p>
                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <a href="{{ route('filament.players.auth.register') }}" class="bg-purple-600 hover:bg-purple-500 px-8 py-4 rounded-lg font-bold transition-all group">
                        <i class="fas fa-shield-alt mr-2"></i>Join as Competitor
                    </a>
                    <a href="{{ route('filament.organizers.auth.register') }}" class="bg-blue-600 hover:bg-blue-500 px-8 py-4 rounded-lg font-bold transition-all group">
                        <i class="fas fa-crown mr-2"></i>Host Tournament
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-20 bg-stone-900/50">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-16 bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
                Arena Features
            </h2>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Player Card -->
                <div class="bg-stone-800 p-8 rounded-xl card-hover">
                    <div class="text-purple-400 text-4xl mb-6">
                        <i class="fas fa-helmet-battle"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">For Gladiators</h3>
                    <ul class="space-y-3 text-stone-300">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                            Instant Tournament Access
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-users text-purple-400 mr-2"></i>
                            Team Management Hub
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-chart-line text-purple-400 mr-2"></i>
                            Real-time Stats Tracking
                        </li>
                    </ul>
                </div>

                <!-- Tournament Card -->
                <div class="bg-stone-800 p-8 rounded-xl card-hover">
                    <div class="text-blue-400 text-4xl mb-6">
                        <i class="fas fa-swords"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Battlegrounds</h3>
                    <ul class="space-y-3 text-stone-300">
                        <li class="flex items-center">
                            <i class="fas fa-crosshairs text-blue-400 mr-2"></i>
                            Precision Matchmaking
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-shield-check text-blue-400 mr-2"></i>
                            Anti-Cheat Systems
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-broadcast-tower text-blue-400 mr-2"></i>
                            Live Stream Integration
                        </li>
                    </ul>
                </div>

                <!-- Organizer Card -->
                <div class="bg-stone-800 p-8 rounded-xl card-hover">
                    <div class="text-purple-400 text-4xl mb-6">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">For Warlords</h3>
                    <ul class="space-y-3 text-stone-300">
                        <li class="flex items-center">
                            <i class="fas fa-chess-clock text-purple-400 mr-2"></i>
                            Tournament Automation
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-coins text-purple-400 mr-2"></i>
                            Prize Pool Management
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-analytics text-purple-400 mr-2"></i>
                            Advanced Analytics
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-stone-800">
        <div class="container mx-auto text-center">
            <div class="">
                <h2 class="text-3xl font-bold mb-6">Ready for Glory?</h2>
                <p class="text-stone-300 mb-8">
                    Join 250,000+ registered competitors and 15,000+ tournaments in the world's
                    fastest-growing esports platform
                </p>
                <div class="flex justify-center gap-6">
                    <a href="{{ route('filament.players.auth.register') }}" class="bg-purple-600 hover:bg-purple-500 px-8 py-4 rounded-lg font-bold transition-all">
                        Start Competing
                    </a>
                    <a href="{{ route('filament.organizers.auth.register') }}" class="bg-blue-600 hover:bg-blue-500 px-8 py-4 rounded-lg font-bold transition-all">
                        Create Tournament
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-stone-900 border-t border-stone-700">
        <div class="container mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <i class="fas fa-gamepad text-purple-400 text-2xl"></i>
                    <div>
                        <h3 class="font-bold">Vidanta Portal</h3>
                        <p class="text-sm text-stone-400">Powered by Vidanta Champions Arena</p>
                    </div>
                </div>
                <div class="flex space-x-6">
                    <a href="https://fb.gg/officialvidanta" class="text-stone-400 hover:text-purple-400">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/officialvidanta/" class="text-stone-400 hover:text-purple-400">
                        <i class="fab fa-instagram"></i>
                    </a>
                    {{-- <a href="#" class="text-stone-400 hover:text-purple-400">
                        <i class="fab fa-steam"></i>
                    </a> --}}
                </div>
            </div>
            <div class="mt-6 text-center text-stone-500 text-sm">
                &copy; 2024 Vidanta Champions Arena. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
