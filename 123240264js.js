window.addEventListener("load", () => {
    alert("Selamat datang di Website ini!");
});

const menuItems = document.querySelectorAll(".menu");
menuItems.forEach(item => {
    item.addEventListener("click", function() {
        menuItems.forEach(link => link.classList.remove("active"));
        this.classList.add("active");
    });
});

function updateClock() {
    const now = new Date();
    const jam = String(now.getHours()).padStart(2, "0");
    const menit = String(now.getMinutes()).padStart(2, "0");
    const detik = String(now.getSeconds()).padStart(2, "0");
    document.getElementById("clock").textContent = `${jam}:${menit}:${detik}`;
}
setInterval(updateClock, 1000);
updateClock();
