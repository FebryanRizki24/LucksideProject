<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-lg">
        <div class="text-center text-2xl font-bold text-gray-800">Status Pembayaran</div>
        <div class="mt-4 text-gray-600 text-lg">
            <p>Halo <strong>{{ $customer_name }}</strong>,</p>
            <p>Terima kasih telah melakukan booking di <strong>{{ $barbershop_name }}</strong>.</p>
            <ul class="mt-2 list-disc list-inside">
                <li><strong>Tanggal:</strong> {{ $booking_date }}</li>
                <li><strong>Waktu:</strong> {{ $booking_time }}</li>
                <li><strong>Barberman:</strong> {{ $barberman_name }}</li>
                <li><strong>Gaya Rambut:</strong> {{ $hairstyle }}</li>
            </ul>

            <p class="mt-4">
                Status Pembayaran Anda: <strong
                    class="{{ $status == 'paid' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($status) }}</strong>
            </p>

            @if ($status == 'paid' && $gcalUrl)
                <p class="mt-4 text-center">
                    <a href="{{ $gcalUrl }}" target="_blank"
                        class="px-6 py-3 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 transition">
                        Tambahkan ke Google Calendar
                    </a>
                </p>
            @elseif ($status == 'pending')
                <p class="mt-4 text-center">
                    <a href="{{ route('dashboard.index') }}"
                        class="px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 transition">Bayar
                        Sekarang</a>
                </p>
            @endif
        </div>
        <div class="mt-6 text-center text-sm text-gray-500">&copy; {{ date('Y') }} {{ $barbershop_name }}. Semua
            Hak Dilindungi.</div>
    </div>
</body>

</html>
