let basket = JSON.parse(localStorage.getItem("basket")) || [];

function addToBasket(name, price) {
    basket.push({ name, price });
    saveBasket();
    renderBasket();
}

function removeItem(index) {
    basket.splice(index, 1);
    saveBasket();
    renderBasket();
}

function saveBasket() {
    localStorage.setItem("basket", JSON.stringify(basket));
}

function renderBasket() {
    const basketDiv = document.getElementById("basket");
    const totalSpan = document.getElementById("total");

    basketDiv.innerHTML = "";
    let total = 0;

    basket.forEach((item, index) => {
        total += item.price;

        const div = document.createElement("div");
        div.className = "basket-item fade-in";

        div.innerHTML = `
            <span>${item.name} — £${item.price.toFixed(2)}</span>
            <button class="remove-btn" onclick="removeItem(${index})">Remove</button>
        `;

        basketDiv.appendChild(div);
    });

    totalSpan.textContent = total.toFixed(2);
}

// Fade-in animation
const style = document.createElement("style");
style.textContent = `
.fade-in {
    opacity: 0;
    transform: translateY(8px);
    animation: fade 0.35s forwards ease-out;
}
@keyframes fade {
    to { opacity: 1; transform: translateY(0); }
}
`;
document.head.appendChild(style);

renderBasket();


