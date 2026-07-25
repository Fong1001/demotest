<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zon Bumijaya Store | Premium Industrial Timber</title>

    <!-- Font: Geist via Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Geist', sans-serif; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-300 min-h-[100dvh] flex flex-col antialiased selection:bg-amber-500 selection:text-zinc-950">
    
    <!-- Header -->
    <header class="border-b border-zinc-800/50 bg-zinc-950/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-500 flex items-center justify-center rounded-sm text-zinc-950 font-bold text-lg">Z</div>
                <span class="font-semibold text-zinc-100 tracking-wide">ZON BUMIJAYA</span>
            </div>
            
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#" class="text-zinc-100 hover:text-amber-500 transition-colors">Products</a>
                <a href="#" class="hover:text-amber-500 transition-colors">Capabilities</a>
                <a href="#" class="hover:text-amber-500 transition-colors">About Us</a>
                <a href="#" class="hover:text-amber-500 transition-colors">Contact</a>
            </nav>
            
            <div class="flex items-center gap-4">
                <a href="/login" class="text-sm font-medium hover:text-amber-500 transition-colors hidden sm:block">Client Login</a>
                <a href="#" class="bg-amber-500 hover:bg-amber-400 text-zinc-950 px-5 py-2.5 text-sm font-semibold rounded-sm transition-colors">Request Quote</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-800/50 bg-zinc-950 pt-16 pb-8">
        <div class="max-w-[1400px] mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-6 h-6 bg-amber-500 flex items-center justify-center rounded-sm text-zinc-950 font-bold text-sm">Z</div>
                    <span class="font-semibold text-zinc-100">ZON BUMIJAYA</span>
                </div>
                <p class="text-zinc-500 max-w-[45ch] text-sm leading-relaxed mb-6">
                    Premium industrial timber and wood products engineered for structural integrity, durability, and global export standards.
                </p>
                <div class="text-sm text-zinc-600">
                    &copy; {{ date('Y') }} Zon Bumijaya Sdn Bhd. All rights reserved.
                </div>
            </div>
            
            <div>
                <h4 class="text-zinc-100 font-semibold mb-6">Products</h4>
                <ul class="space-y-3 text-sm text-zinc-500">
                    <li><a href="#" class="hover:text-amber-500 transition-colors">LVL Beams</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Solid Wood Pallets</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Finger Joint Timber</a></li>
                    <li><a href="#" class="hover:text-amber-500 transition-colors">Custom Millwork</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-zinc-100 font-semibold mb-6">Contact</h4>
                <ul class="space-y-3 text-sm text-zinc-500">
                    <li>sales@zonbumijaya.com</li>
                    <li>+60 3 1234 5678</li>
                    <li class="pt-4">Industrial Park Zone A,<br>Selangor, Malaysia</li>
                </ul>
            </div>
        </div>
    </footer>
    
    <!-- Phosphor Icons Script -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</body>
</html>
