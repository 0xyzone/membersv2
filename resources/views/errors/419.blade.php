<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament Session Expired</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes pulse {
            50% {
                opacity: 0.5;
            }
        }

        .session-bar {
            background: repeating-linear-gradient(-45deg,
                    #57534e,
                    #57534e 5px,
                    #44403c 5px,
                    #44403c 10px);
        }

        .tournament-bracket {
            clip-path: polygon(10% 0, 90% 0, 100% 50%, 90% 100%, 10% 100%, 0% 50%);
        }

    </style>
</head>
<body class="bg-stone-900 text-stone-100 min-h-screen flex flex-col items-center justify-center p-4 overflow-hidden">

    <!-- Main Container -->
    <div class="relative z-10 max-w-2xl w-full text-center space-y-8">
        <!-- Tournament Header -->
        <div class="p-8 bg-stone-800/80 backdrop-blur-sm rounded-xl border-2 border-amber-500/30">
            <div class="w-24 h-24 mx-auto mb-6 bg-stone-700 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold bg-gradient-to-r from-amber-400 to-stone-300 bg-clip-text text-transparent">
                SESSION TIMEOUT
            </h1>
            <p class="text-stone-400 mt-4 font-mono text-sm">
                Tournament match session expired<br>
                <span class="text-amber-400">Protocol 4-1-9 activated</span>
            </p>
        </div>

        <!-- Tournament Session Panel -->
        <div class="mt-6 p-6 bg-stone-800/80 backdrop-blur-sm rounded-xl border-2 border-amber-500/30">
            <div class="font-mono text-sm text-left space-y-4">
                <div class="text-amber-400">> match_status --session=current</div>
                <div class="text-red-400">[ERROR] Session token expired</div>
                <div class="text-stone-400">Last action: 15:32:45</div>

                <!-- Session Timer -->
                <div class="mt-4 space-y-2">
                    <div class="flex items-center justify-between text-stone-400 text-xs">
                        <span>Session Duration:</span>
                        <span>25:00 / 25:00</span>
                    </div>
                    <div class="session-bar h-2 rounded-full overflow-hidden"></div>
                </div>

                <!-- Team Avatars -->
                <div class="flex justify-center items-center space-x-4 mt-6">
                    <div class="w-20 aspect-square bg-stone-700 rounded-full flex items-center justify-center">
                        <span class="text-amber-400 text-xs">TEAM A</span>
                    </div>
                    <div class="text-stone-400 pt-3">vs</div>
                    <div class="w-20 aspect-square bg-stone-700 rounded-full flex items-center justify-center">
                        <span class="text-amber-400 text-xs">TEAM B</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Section -->
        <div class="grid md:grid-cols-2 gap-4 mt-8">
            <a href="{{ url('/') }}" class="p-4 bg-stone-800 hover:bg-stone-700 rounded-xl border-2 border-amber-500/30 transition-all group">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5 text-amber-400 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Return to Lobby</span>
                </div>
            </a>

            <button onclick="renewSession()" class="p-4 bg-stone-800 hover:bg-stone-700 rounded-xl border-2 border-amber-500/30 transition-all group">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5 text-amber-400 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Renew Session</span>
                </div>
            </button>
        </div>

        <!-- Tournament Footer -->
        <div class="text-stone-500 text-sm mt-8">
            <span class="text-amber-400">⏳</span> Session security maintained by tournament servers
        </div>
    </div>

    <!-- Animated Brackets -->
    <div class="fixed inset-0 z-0 opacity-10 pointer-events-none">
        <div class="absolute left-20 top-20 w-32 h-64 border-2 border-stone-600 tournament-bracket"></div>
        <div class="absolute right-20 bottom-20 w-32 h-64 border-2 border-stone-600 tournament-bracket"></div>
    </div>

    <script>
        function renewSession() {
            const button = document.querySelector('button[onclick="renewSession()"]');
            const spinner = button.querySelector('svg');

            // Show loading state
            spinner.classList.remove('hover:animate-spin');
            spinner.classList.add('animate-spin');

            // Simulate API call
            setTimeout(() => {
                spinner.classList.remove('animate-spin');
                spinner.classList.add('hover:animate-spin');
                alert('Session renewal failed - Please start a new match');
            }, 2000);
        }

    </script>
</body>
</html>
