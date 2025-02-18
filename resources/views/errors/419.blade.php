<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Time Sands Expired</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes sand-fall {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translateY(20px);
                opacity: 0;
            }
        }

        .sand-particle {
            animation: sand-fall 2s linear infinite;
        }

        .hourglass-glow {
            filter: drop-shadow(0 0 8px #f59e0b);
        }

    </style>
</head>
<body class="bg-stone-900 text-stone-50 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="max-w-2xl text-center space-y-8">
        <!-- Ancient Hourglass -->
        <div class="relative group" id="hourglass">
            <div class="absolute inset-0 bg-amber-900/20 rounded-full blur-2xl opacity-0 group-hover:opacity-40 transition-opacity"></div>
            <div class="relative p-8 bg-stone-800 rounded-3xl transition-transform duration-300 hover:scale-105 cursor-pointer">
                <div class="relative w-32 h-32 mx-auto">
                    <!-- Sand particles -->
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="sand-particle absolute left-1/4 w-1 h-4 bg-amber-400" style="animation-delay: 0.2s"></div>
                        <div class="sand-particle absolute left-2/4 w-1 h-4 bg-amber-300" style="animation-delay: 0.5s"></div>
                        <div class="sand-particle absolute left-3/4 w-1 h-4 bg-amber-200" style="animation-delay: 0.8s"></div>
                    </div>
                    <!-- Hourglass SVG -->
                    <svg class="w-full h-full hourglass-glow" id="hourglass-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path class="text-amber-600" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path class="text-amber-800" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
                <div class="mt-4 text-amber-400 text-sm font-mono">CHRONOS BREACH</div>
            </div>
        </div>

        <!-- Error Content -->
        <div class="space-y-4">
            <h1 class="text-6xl font-bold text-amber-500">419</h1>
            <h2 class="text-3xl font-semibold">Sands of Time Depleted</h2>
            <p class="text-stone-400 text-lg">
                The arena's temporal gateway has collapsed.<br>
                Even the most patient strategists can't outwait this ancient mechanism!
            </p>
        </div>

        <!-- Interactive Elements -->
        <div class="grid md:grid-cols-2 gap-4 mt-8">
            <a href="{{ url('/') }}" class="p-4 bg-stone-800 hover:bg-stone-700 rounded-lg transition-all group">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-6 h-6 text-amber-400 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Return to Present Era</span>
                </div>
            </a>
            <button onclick="rechargeTime()" class="p-4 bg-stone-800 hover:bg-amber-900/50 rounded-lg transition-all group">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-6 h-6 text-orange-400 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Recharge Temporal Sands</span>
                </div>
            </button>
        </div>

        <!-- Timekeeper's Console -->
        <div class="mt-12 p-4 bg-stone-800/30 rounded border border-amber-900/50 text-left font-mono text-sm">
            <div class="text-amber-400">> temporal_anchor --status</div>
            <div class="text-red-400">[×] Error 0xT1M3: Session expired</div>
            <div class="text-orange-400">[!] Warning: Chronal stability compromised</div>
            <div class="text-amber-400">[✓] New time vortex detected</div>
            <div class="blink">▊</div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-stone-800 rounded-full h-2 mt-4">
            <div id="time-progress" class="bg-amber-500 h-2 rounded-full transition-all duration-1000" style="width: 0%"></div>
        </div>
    </div>

    <footer class="absolute bottom-4 text-stone-500 text-sm">
        Temporal issues? Consult the <a href="mailto:vidantacaofficial@gmail.com" class="text-orange-400 hover:underline">Timekeepers</a>
    </footer>

    <script>
        function rechargeTime() {
            const hourglass = document.getElementById('hourglass');
            const progress = document.getElementById('time-progress');

            // Animate progress bar
            progress.style.width = '30%';
            setTimeout(() => progress.style.width = '60%', 300);
            setTimeout(() => progress.style.width = '90%', 600);
            setTimeout(() => {
                progress.style.width = '100%';
                setTimeout(() => progress.style.width = '0%', 200);
            }, 900);

            // Add hourglass animation
            hourglass.classList.add('hourglass-glow');
            setTimeout(() => hourglass.classList.remove('hourglass-glow'), 1000);

            // Console message
            console.log('%c[Chronos System] Time recharge attempt failed - continuum stabilized', 'color: #f59e0b; font-weight: bold;');
        }

        // Add click handler to hourglass
        document.getElementById('hourglass').addEventListener('click', () => {
            const icon = document.getElementById('hourglass-icon');
            icon.classList.add('animate-flip');
            setTimeout(() => icon.classList.remove('animate-flip'), 1000);
        });

    </script>
</body>
</html>
