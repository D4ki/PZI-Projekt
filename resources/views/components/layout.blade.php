<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Pzi Projekt</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-black text-white font-hanken-grotesk pb-20">
<div class="px-4 sm:px-10">
    <nav class="flex justify-between items-center py-4 border-b border-white/10">
        <!-- Logo -->
        <div>
            <a href="/">
                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt=""
                     class="size-8 rounded-full border border-transparent hover:border-gray-900 transition-colors duration-300">
            </a>
        </div>

        <!-- Desktop links -->
        <div class="hidden md:flex space-x-6 font-bold">
            <x-forms.hover><a href="/jobs">Jobs</a></x-forms.hover>
            <x-forms.hover><a href="/tags">Tags</a></x-forms.hover>
            @auth
                <x-forms.button><a href="/pametniImport">Pametni import</a></x-forms.button>
            @endauth
        </div>

        <!-- Desktop auth links -->
        <div class="hidden md:flex space-x-6 font-bold">
            @auth
                <x-forms.hover><a href="/jobs/create">Post a Job</a></x-forms.hover>
                <form method="POST" action="/logout">
                    @csrf
                    @method('DELETE')
                    <x-forms.button>Log Out</x-forms.button>
                </form>
            @endauth
            @guest
                <x-forms.hover><a href="/register">Sign Up</a></x-forms.hover>
                <x-forms.hover><a href="/login">Log In</a></x-forms.hover>
            @endguest
        </div>

        <!-- Mobile hamburger -->
        <button id="mobile-menu-btn" class="md:hidden text-white focus:outline-none">
            ☰
        </button>
    </nav>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden flex-col space-y-4 mt-4 font-bold md:hidden">
        <x-forms.hover><a href="/jobs" class="block">Jobs</a></x-forms.hover>
        <x-forms.hover><a href="/tags" class="block">Tags</a></x-forms.hover>
        @auth
            <x-forms.hover><a href="/jobs/create" class="block">Post a Job</a></x-forms.hover>
            <x-forms.button><a href="/pametniImport" class="block">Pametni import</a></x-forms.button>
            <form method="POST" action="/logout">
                @csrf
                @method('DELETE')
                <x-forms.button class="block">Log Out</x-forms.button>
            </form>
        @endauth
        @guest
            <x-forms.hover><a href="/register" class="block">Sign Up</a></x-forms.hover>
            <x-forms.hover><a href="/login" class="block">Log In</a></x-forms.hover>
        @endguest
    </div>

    <main class="mt-10 max-w-[986px] mx-auto">
        {{ $slot }}
    </main>
</div>

<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
</body>
</html>
