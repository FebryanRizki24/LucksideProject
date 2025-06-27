document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('slider');
    const boxes = slider.querySelectorAll('.barber-box');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    const desktopMiddleWidth = 636;
    const desktopSideWidth = 270.75;
    const boxHeight = 674;

    let currentIndex = Math.floor(boxes.length / 2);

    function updateSlider() {
        const isMobile = window.innerWidth < 768;
        const sideWidth = isMobile ? 200 : desktopSideWidth;
        const middleWidth = isMobile ? window.innerWidth * 0.9 : desktopMiddleWidth;

        const offset = currentIndex - Math.floor(boxes.length / 2);
        slider.style.transform = `translateX(${offset * -sideWidth}px)`;

        boxes.forEach((box, index) => {
            const img = box.querySelector('.barber-image');
            const isMiddle = index === currentIndex;

            if (isMiddle) {
                box.style.width = `${middleWidth}px`;
                box.style.height = `${boxHeight}px`;
                box.style.backgroundColor = '#0A0A12';
                box.style.display = 'flex';
                box.style.flexDirection = 'column';
                box.style.justifyContent = 'flex-end';
                box.style.alignItems = 'center';
                box.style.overflow = 'hidden';

                img.style.width = '100%';
                img.style.height = 'auto';
                img.style.maxHeight = 'calc(100% - 100px)';
                img.style.objectFit = 'contain';

                moveInfoToBox(box);
            } else {
                box.style.width = `${sideWidth}px`;
                box.style.height = `${boxHeight}px`;
                box.style.backgroundColor = 'white';
                box.style.display = 'flex';
                box.style.justifyContent = 'center';
                box.style.alignItems = 'center';
                box.style.overflow = 'hidden';

                img.style.width = '100%';
                img.style.height = 'auto';
                img.style.maxHeight = '100%';
                img.style.objectFit = 'contain';

                hideInfo(box);
            }
        });

        prevBtn.style.display = currentIndex === 0 ? 'none' : 'block';
        nextBtn.style.display = currentIndex === boxes.length - 1 ? 'none' : 'block';
    }

    function moveInfoToBox(targetBox) {
        document.querySelectorAll('.infoText, .infoIcons').forEach(el => el.style.display = 'none');
        const infoText = targetBox.querySelector('.infoText');
        const infoIcons = targetBox.querySelector('.infoIcons');
        if (infoText && infoIcons) {
            infoText.style.display = 'block';
            infoIcons.style.display = 'flex';
        }
    }

    function hideInfo(box) {
        box.querySelectorAll('.infoText, .infoIcons').forEach(el => {
            el.style.display = 'none';
        });
    }

    prevBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateSlider();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentIndex < boxes.length - 1) {
            currentIndex++;
            updateSlider();
        }
    });

    // Hover effect only on desktop
    boxes.forEach((box, index) => {
        box.addEventListener('mouseenter', () => {
            if (window.innerWidth >= 768) {
                currentIndex = index;
                updateSlider();
            }
        });
    });

    // Modal logic tetap
    const openModalBtns = document.querySelectorAll('.openReviewModal');
    const closeModalBtn = document.getElementById('closeReviewModal');
    const reviewModal = document.getElementById('reviewModal');

    if (closeModalBtn && reviewModal) {
        openModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                reviewModal.classList.remove('hidden');
            });
        });

        closeModalBtn.addEventListener('click', () => {
            reviewModal.classList.add('hidden');
        });

        reviewModal.addEventListener('click', (e) => {
            if (e.target === reviewModal) {
                reviewModal.classList.add('hidden');
            }
        });
    }

    openModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const barberId = btn.getAttribute('data-barber-id');
            reviewModal.classList.remove('hidden');

            const reviewList = document.getElementById('reviewList');
            reviewList.innerHTML = '<p class="text-gray-500 text-center" id="reviewLoading">Memuat review...</p>';

            fetch(`/review/barberman/${barberId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat review');
                    return response.json();
                })
                .then(data => {
                    if (data.data.length === 0) {
                        reviewList.innerHTML = '<p class="text-gray-500 text-center">Belum ada ulasan.</p>';
                        return;
                    }

                    reviewList.innerHTML = data.data.map(review => `
                        <div class="border p-4 rounded shadow mb-2">
                            <p><strong>Rating:</strong> ${review.rating} ⭐</p>
                            <p><strong>Review:</strong> ${review.review}</p>
                            <p class="text-gray-500 text-sm">Dari: ${review.user_name}</p>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    reviewList.innerHTML = '<p class="text-red-500 text-center">Terjadi kesalahan saat memuat ulasan.</p>';
                    console.error(error);
                });
        });
    });

    // Responsif saat resize jendela
    window.addEventListener('resize', updateSlider);

    updateSlider();
});