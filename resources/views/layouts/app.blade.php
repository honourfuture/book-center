<!doctype html>
<head>
    <!-- ... --->
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="/css/full.css" rel="stylesheet" type="text/css"/>
    <link href="/css/public.css" rel="stylesheet" type="text/css"/>
    <link href="/css/tailwind.min.css" rel="stylesheet" type="text/css"/>
    <script src="/js/jquery.min.js"></script>
</head>
<body>
<div class="min-h-full">
<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="http://m.juzhishuwu.com/favicon.ico" alt="Your Company">
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/tool" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">章节计算工具</a>
                        <a href="/tool/diff" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">ID比对</a>
                        <a href="/hand-articles" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">手动更新</a>
                        <a href="/article/14554" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">错误章节</a>
                        <a href="/error-articles?article_ids=19" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">Error-articles</a>
                        <a href="/search-spider" class="bg-gray-900 text-white rounded-md px-3 py-2 text-sm font-medium" aria-current="page">蜘蛛</a>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button type="button" class="inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <!-- Menu open: "hidden", Menu closed: "block" -->
                    <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!-- Menu open: "block", Menu closed: "hidden" -->
                    <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

</nav>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Welcome</h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        @yield('content')
    </div>
</main>
</div>
</body>
