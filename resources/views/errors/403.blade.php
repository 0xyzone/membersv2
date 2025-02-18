<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Lockdown Activated</title>
    @vite('resources/css/app.css')
    <style>
        /* Matrix Background */
        .matrix-bg {
            background-image:
                linear-gradient(rgba(79, 70, 229, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79, 70, 229, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Animations */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(5px);
            }

            75% {
                transform: translateX(-5px);
            }
        }

        @keyframes pulse {
            50% {
                opacity: 0.5;
            }
        }

        .shake {
            animation: shake 0.4s ease-in-out;
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Cyber Elements */
        .cyber-glow {
            box-shadow: 0 0 15px rgba(79, 70, 229, 0.3);
        }

    </style>
</head>
<body class="bg-stone-950 text-stone-100 min-h-screen flex flex-col items-center justify-center p-4 overflow-hidden matrix-bg">

    <!-- Main Security Panel -->
    <div class="relative z-10 max-w-2xl w-full text-center space-y-8">
        <!-- Access Denied Header -->
        <div class="p-8 bg-stone-900/90 backdrop-blur-sm rounded-xl border-2 border-red-500/30 relative overflow-hidden cyber-glow">
            <div class="relative">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-tr from-red-500 to-blue-500 mask mask-circle p-1">
                    <div class="w-full h-full bg-stone-900 mask mask-circle flex items-center justify-center">
                        <svg class="w-12 h-12 text-red-500 pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-3xl font-bold bg-gradient-to-r from-red-400 to-blue-400 bg-clip-text text-transparent">
                    SECURITY LOCKDOWN
                </h1>
                <p class="text-stone-400 mt-4 font-mono text-sm">
                    Restricted zone access attempt logged<br>
                    <span class="text-red-400">Protocol 4-0-3 engaged</span>
                </p>
            </div>
        </div>

        <!-- Security Console -->
        <div class="mt-6 p-6 bg-stone-900/80 backdrop-blur-sm rounded-xl border-2 border-blue-500/30 cyber-glow">
            <div class="font-mono text-sm text-left space-y-4">
                <div class="text-blue-400">> access_request --zone=restricted</div>
                <div class="text-red-400">[ALERT] Invalid security clearance</div>

                <!-- Secret Code Input -->
                <div class="mt-4 space-y-2">
                    <div class="flex items-center border-b border-blue-500/30 pb-1">
                        <span class="text-blue-400 pr-2">$</span>
                        <input type="text" class="flex-1 bg-transparent focus:outline-none" placeholder="Enter override code..." id="accessCode" onkeyup="checkAccessCode(this)">
                    </div>
                    <div class="text-xs min-h-4" id="securityFeedback"></div>
                </div>
            </div>
        </div>

        <!-- Navigation Section -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
            <a href="{{ url('/') }}" class="px-8 py-3 bg-stone-900 hover:bg-stone-800 border-2 border-blue-500/30 rounded-lg transition-all group">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5 text-blue-400 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Return to Safe Zone</span>
                </div>
            </a>
        </div>

        <!-- Security Footer -->
        <div class="text-stone-500 text-sm mt-8">
            <span class="text-red-400">⚠</span> All access attempts are monitored
        </div>
    </div>

    <!-- Floating Particles -->
    <div class="fixed inset-0 z-0 opacity-10 pointer-events-none">
        <div class="particle absolute w-1 h-1 bg-blue-400 rounded-full"></div>
        <div class="particle absolute w-1 h-1 bg-red-400 rounded-full"></div>
    </div>

    <script>
        // Configuration
        const SECRET_CODE = "ESCAPE403"; // Change this secret code
        const REDIRECT_DELAY = 1500; // milliseconds
        const MAX_ATTEMPTS = 5;

        let attempts = 0;

        function checkAccessCode(input) {
            const feedback = document.getElementById('securityFeedback');

            if (input.value === SECRET_CODE) {
                // Valid code
                feedback.innerHTML = '<span class="text-green-400">✓ Access granted. Redirecting...</span>';
                input.classList.remove('shake', 'border-red-500');
                input.classList.add('border-green-500');

                setTimeout(() => {
                    window.location.href = "{{ url('/') }}";
                }, REDIRECT_DELAY);

            } else if (input.value.length >= SECRET_CODE.length) {
                // Invalid code
                attempts++;
                feedback.innerHTML = `<span class="text-red-400">✗ Invalid code (${attempts}/${MAX_ATTEMPTS})</span>`;
                input.classList.add('shake', 'border-red-500');

                if (attempts >= MAX_ATTEMPTS) {
                    feedback.innerHTML += '<div class="text-red-400 mt-1">Security lockdown extended</div>';
                    input.disabled = true;
                    setTimeout(() => {
                        input.value = '';
                        input.disabled = false;
                        attempts = 0;
                    }, 5000);
                }

                setTimeout(() => {
                    input.value = '';
                    input.classList.remove('shake', 'border-red-500');
                    if (attempts < MAX_ATTEMPTS) feedback.textContent = '';
                }, 1000);
            }
        }

        // Initialize particles
        function createParticles() {
            const container = document.querySelector('.particle');
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = `particle absolute w-0.5 h-0.5 bg-${
                    Math.random() > 0.5 ? 'blue' : 'red'
                }-400 rounded-full`;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                particle.style.animation = `float ${5 + Math.random() * 10}s infinite`;
                particle.style.animationDelay = `${Math.random() * 5}s`;
                container.appendChild(particle);
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', createParticles);

    </script>
</body>
</html>
