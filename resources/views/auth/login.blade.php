<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk SIG Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow p-8">
        <h1 class="text-2xl font-bold text-blue-800">SIG Sekolah</h1>
        <p class="text-gray-600 mb-6">Masuk untuk mengelola data sekolah.</p>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block font-medium">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}" required autofocus
                       class="w-full border rounded px-3 py-2">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block font-medium">Kata Sandi</label>
                <input type="password" id="password" name="password" required
                       class="w-full border rounded px-3 py-2">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1"> Ingat saya
            </label>
            <button class="w-full py-2 bg-blue-700 text-white rounded">
                Masuk
            </button>
        </form>

        <a href="{{ url('/') }}" class="block mt-4 text-sm text-blue-700">
            &larr; Kembali ke peta publik
        </a>
    </div>
</body>
</html>
