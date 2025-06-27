document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("scrollable_recommendation");
    const scrollLeftBtn = document.getElementById("scrollLeft");
    const scrollRightBtn = document.getElementById("scrollRight");

    scrollLeftBtn.addEventListener("click", function () {
        container.scrollBy({ left: -250, behavior: "smooth" });
    });

    scrollRightBtn.addEventListener("click", function () {
        container.scrollBy({ left: 250, behavior: "smooth" });
    });
});