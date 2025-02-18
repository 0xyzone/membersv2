<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Level Not Found</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes glitch {
            0% {
                clip-path: inset(20% 0 30% 0);
            }

            5% {
                clip-path: inset(10% 0 35% 0);
            }

            10% {
                clip-path: inset(40% 0 20% 0);
            }

            15% {
                clip-path: inset(25% 0 30% 0);
            }

            20% {
                clip-path: inset(15% 0 25% 0);
            }

            25% {
                clip-path: inset(30% 0 10% 0);
            }

            30% {
                clip-path: inset(5% 0 40% 0);
            }

            35% {
                clip-path: inset(25% 0 25% 0);
            }

            100% {
                clip-path: inset(0 0 0 0);
            }
        }

        .glitch-text {
            position: relative;
            color: #fff;
            text-shadow: 3px 3px 0 #4f46e5, -3px -3px 0 #ef4444;
            animation: glitch 2s infinite linear alternate;
        }

        .neon-pulse {
            animation: pulse 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }

        @keyframes pulse {
            50% {
                opacity: 0.5;
            }
        }

    </style>
</head>
<body class="bg-stone-900 text-white min-h-screen flex flex-col items-center justify-center p-4 overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0 opacity-20 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImEiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTTAgMGg0MHY0MEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0xMSAwbDIgMjAgTDI5IDB6TTAgMTlsNDAgMTlNMzkgMGwxIDIwTTAgOSA0MCA5TTkgMCA5IDQwIiBzdHJva2U9IiM0ZjQ2ZTUiIHN0cm9rZS13aWR0aD0iMSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3QgZmlsbD0idXJsKCNhKSIgb3BhY2l0eT0iMC4xIiB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIi8+PC9zdmc+')]"></div>

    <div class="relative z-10 max-w-2xl text-center space-y-8">
        <!-- Glitchy Game Logo -->
        <div class="glitch-text text-7xl font-bold mb-8">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-pink-500">404</span>
        </div>

        <!-- Error Message -->
        <div class="space-y-4">
            <h2 class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">
                MAP NOT FOUND
            </h2>
            <p class="text-stone-400 text-lg">
                Critical error: Game coordinates corrupted<br>
                <span class="text-sm opacity-75">(0x)1T3M_N07_F0UND</span>
            </p>
        </div>

        <!-- Gaming-style Navigation -->
        <div class="grid md:grid-cols-2 gap-4 mt-8">
            <a href="{{ url('/') }}" class="p-6 bg-stone-800 hover:bg-stone-700 rounded-xl border-2 border-blue-500 hover:border-blue-400 transition-all group relative overflow-hidden">
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="font-medium">Return to Lobby</span>
                </div>
                <div class="absolute inset-0 border-2 border-blue-400 opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </a>

            <button onclick="attemptRepair()" class="p-6 bg-stone-800 hover:bg-stone-700 rounded-xl border-2 border-purple-500 hover:border-purple-400 transition-all group relative overflow-hidden">
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <span class="font-medium">Attempt System Repair</span>
                </div>
                <div class="absolute inset-0 border-2 border-purple-400 opacity-0 group-hover:opacity-20 transition-opacity"></div>
            </button>
        </div>

        <!-- Game Console Display -->
        <div class="mt-12 p-6 bg-black/50 rounded-xl border-2 border-blue-500/30 text-left font-mono text-sm">
            <div class="text-blue-400">> load_map --coordinate=404</div>
            <div class="text-red-400 mt-2">[ERROR] Texture package missing</div>
            <div class="text-yellow-400">[WARNING] Physics mesh corrupted</div>
            <div class="text-green-400">[SUCCESS] Player position reset</div>
            <div class="flex items-center mt-2">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2 neon-pulse"></div>
                <div class="flex-1 bg-stone-800 h-1 rounded">
                    <div class="bg-green-500 w-2/3 h-1 rounded transition-all duration-1000"></div>
                </div>
            </div>
        </div>

        <!-- Spectator Count -->
        <div class="text-stone-400 text-sm mt-4 flex items-center justify-center space-x-2">
            <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
            </svg>
            <span>127 spectators watching this error</span>
        </div>
    </div>

    <!-- CRT Effect -->
    <div class="fixed inset-0 pointer-events-none z-20" style="background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06)); background-size: 100% 2px, 3px 100%;"></div>

    <script>
        function attemptRepair() {
            const progress = document.querySelector('.bg-green-500.w-2/3');
            progress.style.width = Math.random() * 30 + 50 + '%';

            // Add fake error messages
            const consoleElement = document.querySelector('.font-mono');
            const newError = document.createElement('div');
            newError.className = 'text-red-400 mt-2';
            newError.textContent = `[ERROR] Repair failed (attempt ${Math.floor(Math.random() * 100)})`;
            consoleElement.appendChild(newError);

            // Scroll console to bottom
            consoleElement.scrollTop = consoleElement.scrollHeight;
        }

    </script>
</body>
</html>
