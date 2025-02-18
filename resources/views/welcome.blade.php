<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidanta Portal - Competitive Gaming Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script> --}}
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
</head>
<body class="bg-stone-900 text-white">
    <!-- Hero Section -->
    <header class="relative bg-gradient-to-r from-blue-800 to-purple-900 py-20 px-4">
        <div class="container mx-auto text-center">
            <h1 class="text-5xl font-bold mb-6">Compete. Organize. Dominate.</h1>
            <p class="text-xl mb-8">Your premier platform for esports tournaments and team management</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('filament.players.auth.register') }}" class="bg-green-500 hover:bg-green-600 px-8 py-3 rounded-lg font-semibold transition-all">
                    Join as Player
                </a>
                <a href="{{ route('filament.organizers.auth.register') }}" class="bg-purple-500 hover:bg-purple-600 px-8 py-3 rounded-lg font-semibold transition-all">
                    Organize Events
                </a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-16 bg-neutral-900">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-12">Why Choose TournamentHub?</h2>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Player Features -->
                <div class="bg-neutral-800 p-6 rounded-xl">
                    <i class="fas fa-users text-4xl text-green-400 mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">For Players</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                            Join tournaments easily
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                            Create & manage teams
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                            Track your registrations
                        </li>
                    </ul>
                </div>

                <!-- Tournament Features -->
                <div class="bg-neutral-800 p-6 rounded-xl">
                    <i class="fas fa-trophy text-4xl text-yellow-400 mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">Tournaments</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-yellow-400 mr-2"></i>
                            Diverse competitions
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-yellow-400 mr-2"></i>
                            Real-time updates
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-yellow-400 mr-2"></i>
                            Secure participation
                        </li>
                    </ul>
                </div>

                <!-- Organizer Features -->
                <div class="bg-neutral-800 p-6 rounded-xl">
                    <i class="fas fa-chart-line text-4xl text-purple-400 mb-4"></i>
                    <h3 class="text-2xl font-bold mb-4">For Organizers</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                            Host tournaments
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                            Manage registrations
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                            Detailed analytics
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-slate-900">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Get Started?</h2>
            <p class="mb-8 text-stone-300">Join thousands of players and organizers in the ultimate competitive platform</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('filament.players.auth.register') }}" class="bg-green-500 hover:bg-green-600 px-8 py-3 rounded-lg font-semibold transition-all">
                    Start Playing
                </a>
                <a href="{{ route('filament.organizers.auth.register') }}" class="bg-purple-500 hover:bg-purple-600 px-8 py-3 rounded-lg font-semibold transition-all">
                    Host Tournament
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-stone-800 py-8">
        <div class="container mx-auto px-4 text-center text-stone-400">
            <p>&copy; 2024 TournamentHub. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
