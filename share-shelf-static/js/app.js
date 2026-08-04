// Share Shelf — static demo app logic
// No backend exists here. "Login" and "cart" are simulated with
// localStorage purely so the UI feels alive when you click around.
// None of this is real authentication or persistence beyond your browser.

const DEMO = {
  getUser() {
    const raw = localStorage.getItem('ss_demo_user');
    return raw ? JSON.parse(raw) : null;
  },
  setUser(user) {
    localStorage.setItem('ss_demo_user', JSON.stringify(user));
  },
  clearUser() {
    localStorage.removeItem('ss_demo_user');
  },
  isAdmin() {
    return localStorage.getItem('ss_demo_admin') === '1';
  },
  setAdmin(on) {
    if (on) localStorage.setItem('ss_demo_admin', '1');
    else localStorage.removeItem('ss_demo_admin');
  },
  getCart() {
    const raw = localStorage.getItem('ss_demo_cart');
    return raw ? JSON.parse(raw) : [];
  },
  addToCart(itemId) {
    const cart = DEMO.getCart();
    const existing = cart.find(c => c.id === itemId);
    if (existing) existing.qty += 1;
    else cart.push({ id: itemId, qty: 1 });
    localStorage.setItem('ss_demo_cart', JSON.stringify(cart));
  },
  removeFromCart(itemId) {
    const cart = DEMO.getCart().filter(c => c.id !== itemId);
    localStorage.setItem('ss_demo_cart', JSON.stringify(cart));
  },
  clearCart() {
    localStorage.removeItem('ss_demo_cart');
  },
  getMyListings() {
    const raw = localStorage.getItem('ss_demo_my_listings');
    return raw ? JSON.parse(raw) : [];
  },
  addMyListing(listing) {
    const listings = DEMO.getMyListings();
    listings.unshift(listing);
    localStorage.setItem('ss_demo_my_listings', JSON.stringify(listings));
  },
};

function renderNavbar() {
  const mount = document.getElementById('navbar-mount');
  if (!mount) return;

  const user = DEMO.getUser();
  const admin = DEMO.isAdmin();
  const cartCount = DEMO.getCart().reduce((n, c) => n + c.qty, 0);

  let links = '';
  if (admin) {
    links = `
      <li class="nav-item"><a class="nav-link" href="admin.html">Dashboard</a></li>
      <li class="nav-item"><span class="nav-link text-light">👤 Admin</span></li>
      <li class="nav-item"><a class="nav-link text-danger" href="#" onclick="DEMO.setAdmin(false); window.location='index.html'; return false;">Logout</a></li>
    `;
  } else if (user) {
    links = `
      <li class="nav-item"><a class="nav-link" href="browse.html">Browse</a></li>
      <li class="nav-item"><a class="nav-link" href="add-item.html">Sell / Donate</a></li>
      <li class="nav-item"><a class="nav-link" href="cart.html">🛒 Cart${cartCount ? ` (${cartCount})` : ''}</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">${user.name}</a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="profile.html">My Profile</a></li>
          <li><a class="dropdown-item" href="my-listings.html">My Listings</a></li>
          <li><a class="dropdown-item" href="my-purchases.html">My Purchases</a></li>
          <li><a class="dropdown-item" href="my-claims.html">My Claims</a></li>
          <li><a class="dropdown-item" href="support.html">Support</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#" onclick="DEMO.clearUser(); window.location='index.html'; return false;">Logout</a></li>
        </ul>
      </li>
    `;
  } else {
    links = `
      <li class="nav-item"><a class="nav-link" href="browse.html">Browse</a></li>
      <li class="nav-item"><a class="nav-link" href="login.html">Login</a></li>
      <li class="nav-item"><a class="nav-link" href="register.html">Register</a></li>
    `;
  }

  mount.outerHTML = `
  <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color:${admin ? '#212529' : '#2f6f4f'};">
    <div class="container">
      <a class="navbar-brand fw-bold" href="${admin ? 'admin.html' : 'index.html'}">${admin ? '🔒 Share Shelf Admin' : '♻️ Share Shelf'}</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMain">
        ${!admin ? `
        <form class="d-flex mx-auto my-2 my-lg-0" style="max-width:400px;width:100%;" onsubmit="window.location='browse.html?q='+encodeURIComponent(this.q.value); return false;">
          <input class="form-control" type="search" name="q" placeholder="Search items...">
          <button class="btn btn-light ms-2" type="submit">Search</button>
        </form>` : ''}
        <ul class="navbar-nav ms-auto align-items-lg-center">
          ${links}
        </ul>
      </div>
    </div>
  </nav>`;
}

function renderFooter() {
  const mount = document.getElementById('footer-mount');
  if (!mount) return;
  mount.outerHTML = `
  <footer class="text-center text-muted py-4 mt-5 border-top">
    <small>&copy; 2026 Share Shelf — CSE311L Database Systems Lab, North South University</small>
    <div class="small mt-1">This is a static UI demo (GitHub Pages). No real login or database — see the XAMPP/PHP build for the working version.</div>
  </footer>`;
}

function requireDemoLogin() {
  if (!DEMO.getUser()) {
    window.location = 'login.html';
  }
}

function requireDemoAdmin() {
  if (!DEMO.isAdmin()) {
    window.location = 'alogin.html';
  }
}

function moneyBDT(n) {
  return '৳' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.addEventListener('DOMContentLoaded', () => {
  renderNavbar();
  renderFooter();
});
