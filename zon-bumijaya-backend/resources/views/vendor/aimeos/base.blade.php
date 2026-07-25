<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		@if( config('app.debug') !== true )
			<meta http-equiv="Content-Security-Policy" content="base-uri 'self'; default-src 'self' 'nonce-{{ app( 'aimeos.context' )->get()->nonce() }}'; {{ config( 'shop.csp.frontend', 'style-src \'unsafe-inline\' \'self\'; img-src \'self\' data: https://aimeos.org; frame-src https://www.youtube.com https://player.vimeo.com' ) }}">
		@endif

		@if( in_array(app()->getLocale(), ['ar', 'az', 'dv', 'fa', 'he', 'ku', 'ur']) )
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.rtl.css?v=' . config( 'shop.version', 1 ) ) }}">
		@else
			<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/app.css?v=' . config( 'shop.version', 1 ) ) }}">
		@endif
		<link type="text/css" rel="stylesheet" href="{{ asset('vendor/shop/themes/default/aimeos.css?v=' . config( 'shop.version', 1 ) ) }}">

        <!-- Tailwind Vite for Custom Branding -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

		@yield('aimeos_header')

		<style nonce="{{ app( 'aimeos.context' )->get()->nonce() }}">
			:root {
				@foreach( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getConfigValue( 'theme/default', [] ) as $key => $value )
					{{ $key }}: {{ $value }};
				@endforeach
			}
		</style>

		<link rel="icon" href="{{ asset( app( 'aimeos.context' )->get()->config()->get( 'resource/fs-media/baseurl' ) . '/' . ( app( 'aimeos.context' )->get()->locale()->getSiteItem()->getIcon() ?: '../vendor/shop/themes/default/assets/icon.png' ) ) }}">
	</head>
	<body class="{{ $page ?? '' }} bg-background text-on-background dark">
		
        <!-- Zon Bumijaya Custom Header -->
        <header class="w-full z-50 bg-background/80 backdrop-blur-xl border-b border-white/5 py-4 sticky top-0">
            <div class="max-w-[1280px] mx-auto px-8 flex justify-between items-center">
                <div class="text-[#e9c176] text-2xl tracking-tighter" style="font-family: var(--font-playfair), serif;">
                    Zon Bumijaya Store
                </div>
                
                <!-- Aimeos Injected Search & Nav -->
                <div class="flex items-center gap-6">
                    @yield('aimeos_head_search')
                    @yield('aimeos_head_locale')
                    
                    <nav class="hidden md:flex gap-6 uppercase text-[11px] tracking-[0.2em] font-semibold text-white items-center">
                        <a href="http://localhost:3000" class="hover:text-[#e9c176] transition-colors border-r border-white/20 pr-6">Back to Site</a>
                        
                        @if (Auth::guest() && config('app.shop_registration'))
                            <a href="{{ airoute( 'register' ) }}" class="hover:text-[#e9c176]">{{ __('Register') }}</a>
                        @endif
                        @if (Auth::guest())
                            <a href="{{ airoute( 'login' ) }}" class="hover:text-[#e9c176]">{{ __( 'Login' ) }}</a>
                        @else
                            <a href="{{ airoute( 'aimeos_shop_account' ) }}" class="hover:text-[#e9c176]">{{ __( 'Account' ) }}</a>
                            <form id="logout" action="{{ airoute( 'logout' ) }}" method="POST" class="inline">
                                {{ csrf_field() }}
                                <button type="submit" class="uppercase text-[11px] tracking-[0.2em] font-semibold text-white hover:text-[#e9c176]">{{ __( 'Logout' ) }}</button>
                            </form>
                        @endif
                    </nav>

                    <div class="ml-4">
                        @yield('aimeos_head_basket')
                    </div>
                </div>
            </div>
        </header>

		<div class="content">
			@yield('aimeos_stage')
			<main>
				@yield('aimeos_body')
				@yield('content')
			</main>
		</div>


		<footer class="bg-background border-t border-white/5 py-12 px-8 mt-24">
			<div class="max-w-[1280px] mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
				<div>
					<h2 class="text-[#e9c176] text-2xl tracking-tighter" style="font-family: var(--font-playfair), serif;">Zon Bumijaya Store</h2>
					<p class="text-white/40 text-sm mt-4">Industrial Timber Solutions</p>
				</div>
				<div class="text-right text-white/30 text-xs">
					&copy; 2024 Zon Bumijaya. All rights reserved.
				</div>
			</div>
		</footer>



		<a id="toTop" class="back-to-top" href="#" title="{{ __( 'Back to top' ) }}">
			<div class="top-icon"></div>
		</a>

		<!-- Scripts -->
		<script src="{{ asset('vendor/shop/themes/default/app.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		<script src="{{ asset('vendor/shop/themes/default/aimeos.js?v=' . config( 'shop.version', 1 ) ) }}"></script>
		@yield('aimeos_scripts')
	</body>
</html>
