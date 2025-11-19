<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Mart - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
        rel="stylesheet">

    <link 
      href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" 
      rel="stylesheet">
    
</head>
<body class="bg-linear-to-br from-green-50 via-emerald-50 to-green-100 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white/80 backdrop-blur-lg shadow-2xl shadow-green-200/50 rounded-2xl w-full @yield('auth-width', 'max-w-md') border border-green-200/30">
        
        <div class="text-center p-6 pb-4 border-b border-gray-100">
            <span class="text-6xl" aria-label="Green Mart Logo">🌿</span>

            <h1 class="text-4xl font-bold mt-2" style="font-family: 'Fredoka', sans-serif;">
                <span class="text-green-600">Green</span><span class="text-amber-500">Mart</span>
            </h1>

            <p class="text-gray-500 mt-1">@yield('heading')</p>
        </div>

        <div class="p-6">
            @yield('content')
        </div>
    </div>

</body>
</html>