document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("button[data-toggle]").forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-toggle");
            toggleAccordion(id);
        });
    });
});

function toggleAccordion(id) {
    const content = document.getElementById("content-" + id);
    const icon = document.getElementById("icon-" + id);

    content.classList.toggle("hidden");
    icon.innerText = content.classList.contains("hidden") ? "▼" : "▲";
}