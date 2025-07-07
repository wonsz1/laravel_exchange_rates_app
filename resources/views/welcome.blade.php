<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Exchange Rates App</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        <style>
            body {
                font-family: 'instrument-sans', sans-serif;
                background-color: #f8f9fa;
                color: #1b1b18;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 2rem;
            }
            .title {
                font-size: 2.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
                color: #1b1b18;
            }
            .description {
                font-size: 1.1rem;
                line-height: 1.6;
                margin-bottom: 2rem;
                color: #495057;
            }
            .cta-button {
                display: inline-block;
                padding: 0.75rem 1.5rem;
                background-color: #1b1b18;
                color: white;
                text-decoration: none;
                border-radius: 0.375rem;
                transition: background-color 0.2s;
            }
            .cta-button:hover {
                background-color: #323232;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="title">Exchange Rates App</h1>
            <p class="description">Welcome to our exchange rates application. View and manage currency exchange rates with ease.</p>
            <a href="{{ route('currencies.index') }}" class="cta-button">View Currencies</a>
            <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
            </nav>
            
        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
