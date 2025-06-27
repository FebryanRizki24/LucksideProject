<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Akun Anda Berhasil Dibuat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <div class="text-center text-2xl font-bold text-gray-800">Selamat, Akun Anda Telah Dibuat!</div>
        <div class="mt-4 text-gray-600 text-lg">
            <p>Halo,</p>
            <p>Terima kasih telah mendaftar melalui Google. Berikut adalah password yang telah kami buat untuk akun Anda:</p>

            <p class="mt-4 text-center font-semibold text-xl bg-gray-200 p-3 rounded-lg">{{ $password }}</p>

            <p class="mt-4">Kami menyarankan Anda untuk segera mengganti password ini melalui pengaturan akun Anda.</p>

            <p class="mt-6 text-center">
                <a href="{{ url('/login') }}" class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition">Login Sekarang</a>
            </p>
            
            <p class="mt-6">Jika Anda tidak merasa melakukan pendaftaran, silakan abaikan email ini.</p>
        </div>
        <div class="mt-6 text-center text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. Semua Hak Dilindungi.</div>
    </div>
</body>
</html>