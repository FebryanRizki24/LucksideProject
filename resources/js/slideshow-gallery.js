document.addEventListener("DOMContentLoaded", function () {
    const slideshow = document.getElementById("slideshow");
    const slideWidth = 323; // 304px width + 19px gap

    // Fungsi untuk memulai animasi pergeseran ke kiri
    function moveSlide() {
        // Geser container ke kiri sejauh 1 slide
        slideshow.style.transition = "transform 0.5s ease-in-out";
        slideshow.style.transform = `translateX(-${slideWidth}px)`;
    }

    // Setelah transisi selesai, pindahkan slide pertama ke akhir
    slideshow.addEventListener("transitionend", function () {
        // Pindahkan elemen pertama ke akhir
        const firstSlide = slideshow.firstElementChild;
        slideshow.appendChild(firstSlide);

        // Reset posisi container tanpa transisi
        slideshow.style.transition = "none";
        slideshow.style.transform = "translateX(0)";

        // Memaksa reflow untuk memastikan reset diterapkan
        slideshow.offsetWidth;

        // Lanjutkan animasi setelah jeda (misalnya 3 detik)
        setTimeout(moveSlide, 3000);
    });

    // Mulai animasi pertama setelah 3 detik
    setTimeout(moveSlide, 3000);
});