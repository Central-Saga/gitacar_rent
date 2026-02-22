<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased font-sans relative overflow-x-hidden text-textDark">
    <!-- Gradient Circle Decoration -->
    <div
        class="absolute -top-32 -left-32 w-[32rem] h-[32rem] bg-gradient-to-br from-primaryLight to-transparent rounded-full blur-2xl pointer-events-none opacity-80">
    </div>

    <div class="flex min-h-svh flex-col p-6 max-w-md mx-auto relative z-10 w-full pt-16 mt-12 md:mt-16">
        <div class="flex flex-col gap-6 w-full">
            {{ $slot }}
        </div>
    </div>
    @fluxScripts
</body>

</html>