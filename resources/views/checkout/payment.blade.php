<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Travelink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Selesaikan Pembayaran</h2>
        <p class="text-gray-500 mb-8 text-sm">Klik tombol di bawah untuk membuka halaman pembayaran aman Midtrans.</p>

        <button id="pay-btn"
            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg text-lg transition duration-200">
            Bayar Sekarang
        </button>
        <p class="text-xs text-gray-400 mt-4">Pembayaran aman diproses oleh Midtrans</p>
    </div>

    <script>
        const snapToken = @json($snapToken);
        const ticketId = @json($ticketId);

        document.getElementById('pay-btn').addEventListener('click', function () {
            window.snap.pay(snapToken, {
                onSuccess: function (result) {
                    window.location.href = '/tickets/' + ticketId;
                },
                onPending: function (result) {
                    alert('Pembayaran pending. Kami akan mengirim konfirmasi setelah pembayaran diterima.');
                    window.location.href = '/tickets';
                },
                onError: function (result) {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    window.location.href = '/schedules';
                },
                onClose: function () {
                    // User menutup popup tanpa bayar
                }
            });
        });

        // Auto-trigger snap popup setelah 1 detik
        setTimeout(function() {
            document.getElementById('pay-btn').click();
        }, 1000);
    </script>
</body>
</html>
