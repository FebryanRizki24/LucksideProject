document.getElementById("waForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Mencegah submit default

    // Ambil nilai input
    let firstName = document.getElementById("firstName").value.trim();
    let lastName = document.getElementById("lastName").value.trim();
    let subject = document.getElementById("subject").value;
    let message = document.getElementById("message").value.trim();

    if (!firstName || !subject || !message) {
        alert("Nama depan, Tujuan, dan Deskripsi harus diisi!");
        return;
    }

    // Nomor WhatsApp tujuan (ganti dengan nomor yang sesuai)
    let waNumber = "62895626350309"; // Format: 62 untuk Indonesia

    // Format pesan untuk WhatsApp
    let waMessage = `Halo, saya ${firstName} ${lastName}.\nSubject: ${subject}\n\n${message}`;
    
    // Encode pesan untuk URL
    let encodedMessage = encodeURIComponent(waMessage);

    // Redirect ke WhatsApp
    let waURL = `https://wa.me/${waNumber}?text=${encodedMessage}`;
    window.open(waURL, "_blank");
});
