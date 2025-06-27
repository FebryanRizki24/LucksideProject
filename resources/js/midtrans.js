document.addEventListener('DOMContentLoaded', function () {
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.addEventListener('click', function () {
            const form = document.getElementById('booking-form');
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    snap.pay(data.snap_token, {
                        onSuccess: function (result) {
                            alert('Booking berhasil!');
                            window.location.href = '/dashboard/booking';
                        },
                        onPending: function (result) {
                            alert('Menunggu pembayaran!');
                            window.location.href = '/dashboard/booking';
                        },
                        onError: function (result) {
                            alert('Transaksi gagal!');
                            console.error(result);
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token pembayaran.');
                    console.error(data);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan saat mengirim data.');
                console.error('Error:', error);
            });            
        });
    }
});