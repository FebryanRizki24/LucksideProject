import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import $ from 'jquery';
import Swal from 'sweetalert2';

// ========== Inisialisasi ==========
document.addEventListener("DOMContentLoaded", function () {
    window.flatpickr = flatpickr;

    initFlatpickr();
    initFormValidation();
    initFaceShapeRecommendation();
    initTermsModal();
    initPayButton();
    checkDateStatus();
});

// ========== Inisialisasi Flatpickr & Time Slots ==========
function initFlatpickr() {
    if ($("#date").length) {
        flatpickr("#date", {
            dateFormat: "d-m-Y",
            locale: Indonesian,
            altInput: true,
            altFormat: "l, d F Y",
            minDate: "today",
            onChange: function (selectedDates, dateStr, instance) {
                if (dateStr) {
                    checkDateStatus(dateStr);
                }
            }
        });
    }

    $('#time').on('click', loadAvailableSlots);

    $('select[name="barberman_id"]').on('change', function () {
        $('#time').val('');
        toggleTimeBackground();
    });
}

function checkDateStatus(dateStr) {
    if (!dateStr) {
        console.warn('Tanggal kosong atau tidak terdefinisi');
        return;
    }
    const formattedDate = dateStr.split('-').reverse().join('-');

    $.post('/booking/check-date', {
        date: formattedDate,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function (response) {
        if (response.message) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: response.message
            });
        }
    }).fail(() => {
        console.error('Gagal memeriksa status tanggal.');
    });
}

function loadAvailableSlots() {
    const barberman_id = $('select[name="barberman_id"]').val();
    const date = $('#date').val();

    if (!barberman_id || !date) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Please select date and barberman first!'
        });
        return;
    }

    $('#timeSlots').html('<li class="p-2 text-center">Loading...</li>');

    $.post('/booking/get-schedule', {
        barberman_id,
        date,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function (response) {
        const slots = response.available_slots || [];
        const slotContainer = $('#timeSlots');
        slotContainer.empty();

        if (slots.length === 0) {
            slotContainer.append('<li class="p-2 text-center text-red-500">No slots available</li>');
        } else {
            const today = new Date().toDateString(); // Format seperti "Thu Apr 10 2025"
            const selectedDate = new Date(date.split('-').reverse().join('-')).toDateString(); // ubah dari dd-mm-yyyy ke yyyy-mm-dd

            const now = new Date();
            slots.forEach(slot => {
                // Cek jika tanggal booking adalah hari ini
                if (selectedDate === today) {
                    // Buat jam booking: "08:30" -> Date dengan waktu yang sama hari ini
                    const [hours, minutes] = slot.split(':');
                    const slotTime = new Date();
                    slotTime.setHours(hours, minutes, 0, 0);

                    // Jika slot sudah lewat, skip
                    if (slotTime <= now) return;
                }

                // Jika belum lewat atau bukan hari ini, tampilkan
                slotContainer.append(`
        <li class="p-2 bg-gray-100 text-center rounded cursor-pointer hover:bg-gray-200" data-slot="${slot}">
            ${slot}
        </li>
    `);
            });
        }

        $('#timeModal').removeClass('hidden');
    }).fail(() => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to fetch available slots.'
        });
    });
}

$(document).on('click', '#timeSlots li', function () {
    const slot = $(this).data('slot');
    $('#time').val(slot);
    toggleTimeBackground();
    closeTimeModal();
});

function toggleTimeBackground() {
    const timeInput = $('#time');
    timeInput.toggleClass('bg-gray-100', !timeInput.val());
}

window.closeTimeModal = function () {
    $('#timeModal').addClass('hidden');
};

// ========== Validasi Form ==========
function initFormValidation() {
    $('form').on('submit', function (e) {
        const date = $('#date').val();
        const time = $('#time').val();

        if (!date) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please select a date.'
            });
            e.preventDefault();
            return;
        }

        if (!time) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Please select a time.'
            });
            e.preventDefault();
        }
    });
}

// ========== Modal Persetujuan ==========
function initTermsModal() {
    const sectionId = 'booking-section';
    const modalId = 'termsModal';
    const acceptedKey = 'bookingTermsAccepted';
    const hasAccepted = localStorage.getItem(acceptedKey) === 'true';

    if (document.getElementById(sectionId) && !hasAccepted) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        document.getElementById(modalId)?.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

window.acceptTerms = function () {
    const modal = document.getElementById('termsModal');
    const spinner = document.getElementById('agree-spinner');
    const button = document.getElementById('agree-button');

    button.classList.add('opacity-70', 'pointer-events-none');
    spinner.classList.remove('hidden');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        localStorage.setItem('bookingTermsAccepted', 'true');
    }, 1000);
};

// ========== Rekomendasi Bentuk Wajah ==========
function initFaceShapeRecommendation() {
    const checkbox = document.getElementById('rekomendasiCheckbox');
    const faceShapeContainer = document.getElementById('faceShapeContainer');
    const faceShapeSelect = document.getElementById('faceShapeSelect');
    const faceShapePreview = document.getElementById('faceshape-preview');
    const faceShapeImg = document.getElementById('faceshape-preview-img');
    const faceShapeName = document.getElementById('faceshape-name-label');
    const faceShapeDesc = document.getElementById('faceshape-desc-label');
    const hairstyleSelect = document.getElementById('hairstyle-select');

    if (!checkbox || !faceShapeContainer) return;

    toggleFaceShape(checkbox.checked);

    checkbox.addEventListener('change', function () {
        toggleFaceShape(this.checked);
    });

    function toggleFaceShape(isChecked) {
        faceShapeContainer.classList.toggle('hidden', !isChecked);
        faceShapePreview.classList.toggle('hidden', true);
        if (!isChecked) {
            faceShapeSelect.value = '';
        }
    }

    faceShapeSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const face_shape_id = this.value;

        // 🔍 Tampilkan preview wajah jika tersedia
        const imageUrl = selectedOption.getAttribute('data-image');
        const shapeName = selectedOption.textContent;
        const shapeDesc = selectedOption.getAttribute('data-deskripsi');

        if (face_shape_id && imageUrl) {
            faceShapeImg.src = imageUrl;
            faceShapeName.textContent = shapeName;
            faceShapeDesc.textContent = shapeDesc || '-';
            faceShapePreview.classList.remove('hidden');
        } else {
            faceShapePreview.classList.add('hidden');
            faceShapeImg.src = '';
            faceShapeName.textContent = '';
            faceShapeDesc.textContent = '';
        }

        // 🔁 Request hairstyle rekomendasi
        if (!face_shape_id) return;

        hairstyleSelect.innerHTML = '<option value="">Loading...</option>';

        $.post('/booking/by-face-shape', {
            face_shape_id,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function (response) {
            hairstyleSelect.innerHTML = '<option value="">Pilih Hairstyle</option>';
            response.hairstyles.forEach(function (style) {
                hairstyleSelect.innerHTML += `
                    <option value="${style.id}"
                        data-image="${style.photo_url}"
                        data-deskripsi="${style.deskripsi}">
                        ${style.name}
                    </option>`;
            });
        }).fail(() => {
            hairstyleSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        });
    });
}

// ========== Spinner Helper ==========
function showTailwindSpinner(buttonId, spinnerId) {
    const button = document.getElementById(buttonId);
    const spinner = document.getElementById(spinnerId);

    button.classList.add('opacity-70', 'pointer-events-none');
    spinner.classList.remove('hidden');
}

// ========== Tombol Bayar + Midtrans Snap ==========
function initPayButton() {
    const payButton = document.getElementById('pay-button');
    if (!payButton) return;

    payButton.addEventListener('click', function () {
        const date = $('#date').val();
        const time = $('#time').val();
        const barberman = $('select[name="barberman_id"]').val();
        const hairstyle = $('#hairstyle-select').val();

        if (!date || !time || !barberman || !hairstyle) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Mohon lengkapi semua data booking sebelum melanjutkan.',
            });
            return;
        }

        showTailwindSpinner('pay-button', 'book-spinner');
        payButton.disabled = true;

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
                        onSuccess: function () {
                            Swal.fire('Sukses!', 'Booking berhasil.', 'success').then(() => {
                                window.location.href = '/dashboard/booking';
                            });
                        },
                        onPending: function () {
                            Swal.fire('Menunggu Pembayaran', 'Silakan selesaikan pembayaran.', 'info').then(() => {
                                window.location.href = '/dashboard/booking';
                            });
                        },
                        onError: function (result) {
                            Swal.fire('Gagal', 'Transaksi gagal.', 'error');
                            console.error(result);
                            payButton.disabled = false;
                            document.getElementById('book-spinner').classList.add('hidden');
                        }
                    });
                } else {
                    Swal.fire('Gagal', 'Gagal mendapatkan token pembayaran.', 'error');
                    console.error(data);
                    payButton.disabled = false;
                    document.getElementById('book-spinner').classList.add('hidden');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan saat mengirim data.', 'error');
                console.error(error);
                payButton.disabled = false;
                document.getElementById('book-spinner').classList.add('hidden');
            });
    });
}
