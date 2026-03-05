const WISHLIST_KEY = "dormWishlist";
const BASKET_KEY = "basketItems"; // must match your basket.js localStorage key

function loadWishlist() {
  return JSON.parse(localStorage.getItem(WISHLIST_KEY)) || [];
}

function saveWishlist(data) {
  localStorage.setItem(WISHLIST_KEY, JSON.stringify(data));
}

function addToBasketFromWishlist(name, price) {
  let basket = JSON.parse(localStorage.getItem(BASKET_KEY)) || [];

  basket.push({ name, price });

  localStorage.setItem(BASKET_KEY, JSON.stringify(basket));

  removeFromWishlist(name);

  // optional: go straight to basket page after moving
  // window.location.href = "basket.html";
}

function removeFromWishlist(name) {
  const updated = loadWishlist().filter(item => item.name !== name);
  saveWishlist(updated);
  renderWishlist();
}

function clearWishlist() {
  saveWishlist([]);
  renderWishlist();
}

function renderWishlist() {
  const container = document.getElementById("wishlist");
  if (!container) return;

  const list = loadWishlist();

  if (list.length === 0) {
    container.innerHTML = "<p>Your wishlist is empty.</p>";
    return;
  }

  container.innerHTML = list.map(item => `
    <div class="row">
      <div>
        <strong>${item.name}</strong><br>
        £${Number(item.price).toFixed(2)}
      </div>
      <div>
        <button onclick="addToBasketFromWishlist('${escapeQuotes(item.name)}', ${Number(item.price)})">
          Move to Basket
        </button>
        <button onclick="removeFromWishlist('${escapeQuotes(item.name)}')">
          Remove
        </button>
      </div>
    </div>
  `).join("");
}

function escapeQuotes(str) {
  return String(str).replace(/'/g, "\\'");
}

document.addEventListener("DOMContentLoaded", renderWishlist);
