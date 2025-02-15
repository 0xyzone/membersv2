<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - Stellar Timeout</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .animate-float {
            animation: float 4s ease-in-out infinite;
        }

        .star {
            animation: twinkle var(--duration) ease-in-out infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 1;
            }
        }

    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 min-h-screen text-white relative overflow-hidden">
    <!-- Starry background -->
    <div class="absolute inset-0 z-0" id="stars"></div>

    <div class="container mx-auto px-4 min-h-screen flex flex-col items-center justify-center relative z-10">
        <div class="max-w-2xl text-center space-y-6">
            <!-- Animated Astronaut SVG -->
            <svg class="w-64 h-64 mx-auto animate-float" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 25C115.464 25 128 37.5359 128 53C128 68.4641 115.464 81 100 81C84.5359 81 72 68.4641 72 53C72 37.5359 84.5359 25 100 25Z" fill="#F3F4F6" />
                <path d="M116 136C116 136 124 128 124 108C124 88 108 84 100 84C92 84 76 88 76 108C76 128 84 136 84 136L116 136Z" fill="#3B82F6" />
                <path d="M100 170C133.137 170 160 143.137 160 110C160 76.8629 133.137 50 100 50C66.8629 50 40 76.8629 40 110C40 143.137 66.8629 170 100 170Z" fill="#1D4ED8" />
                <path d="M100 170L84 200H116L100 170Z" fill="#1E3A8A" />
                <circle cx="88" cy="60" r="8" fill="#111827" />
                <circle cx="112" cy="60" r="8" fill="#111827" />
            </svg>

            <h1 class="text-4xl md:text-5xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                🚀 Interstellar Session Timeout
            </h1>

            <p class="text-lg md:text-xl text-gray-200 leading-relaxed">
                Our quantum entanglement link has dissolved into the cosmic background.<br>
                To protect the space-time continuum, please re-establish your connection.
            </p>

            <div class="mt-8 space-y-4">
                <a href="{{ back() }}" class="inline-block px-8 py-4 text-lg font-bold text-gray-900 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full transform transition-all hover:scale-105 hover:shadow-lg hover:shadow-yellow-400/30">
                    ⚡ Re-Engage Warp Drive
                </a>

                <p class="text-sm text-gray-400 mt-4">
                    Need help? Contact Mission Control at
                    <a href="mailto:support@example.com" class="text-blue-400 hover:text-blue-300">
                        support@example.com
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Generate dynamic stars
        function createStars() {
            const starsContainer = document.getElementById('stars');
            for (let i = 0; i < 100; i++) {
                const star = document.createElement('div');
                star.className = 'absolute bg-white rounded-full star';
                star.style.width = `${Math.random() * 3}px`;
                star.style.height = star.style.width;
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                star.style.setProperty('--duration', `${Math.random() * 3 + 1}s`);
                starsContainer.appendChild(star);
            }
        }
        createStars();

    </script>

    <!-- Optional: Add some space particles animation -->
    @viteReactRefresh
    <script type="module">
        import confetti from 'https://cdn.skypack.dev/canvas-confetti';
        
        document.addEventListener('DOMContentLoaded', () => {
            confetti({
                particleCount: 50,
                spread: 70,
                origin: { y: 0.6 },
                colors: ['#3B82F6', '#60A5FA', '#93C5FD'],
                disableForReducedMotion: true
            });
        });
    </script>
</body>
</html>
