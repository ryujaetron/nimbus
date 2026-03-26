<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home.php">
            <i class="bi bi-shop"></i> CartHive
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'home') ? 'active' : '' ?>" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'categories') ? 'active' : '' ?>" href="category.php">
                        <i class="bi bi-grid-3x3-gap"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($current_page) && $current_page == 'products') ? 'active' : '' ?>" href="products.php">
                        <i class="bi bi-grid"></i> Products
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($current_page) && $current_page == 'orders') ? 'active' : '' ?>" href="order_history.php">
                            <i class="bi bi-clock-history"></i> Orders
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav align-items-center">
                <!-- Wishlist (for all users) -->
                <li class="nav-item">
                    <a class="nav-link" href="wishlist.php" title="Wishlist">
                        <i class="bi bi-heart"></i>
                        <?php
                        $wishlist_count = isset($_SESSION['wishlist_count']) ? $_SESSION['wishlist_count'] : 0;
                        if ($wishlist_count > 0):
                        ?>
                            <span class="badge bg-danger"><?= $wishlist_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Cart (for all users) -->
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="openCart(); return false;" title="Cart">
                        <i class="bi bi-cart"></i>
                        <?php
                        $cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
                        if ($cart_count > 0):
                        ?>
                            <span class="badge bg-danger"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Search Icon (shows by default) -->
                <li class="nav-item" id="searchIcon">
                    <a class="nav-link" href="#" onclick="toggleSearch(event)" title="Search">
                        <i class="bi bi-search"></i>
                    </a>
                </li>

                <!-- Search Form (hidden by default) -->
                <li class="nav-item" id="searchForm" style="display: none;">
                    <form action="search.php" method="GET" class="d-flex" style="width: 300px;">
                        <input type="text"
                               name="q"
                               id="navbarSearchInput"
                               class="form-control form-control-sm"
                               placeholder="Search..."
                               required
                               autocomplete="off"
                               style="background: rgba(255, 255, 255, 0.9); border-color: rgba(255, 255, 255, 0.5);">
                        <button class="btn btn-sm btn-light ms-1" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light ms-1" type="button" onclick="toggleSearch(event)">
                            <i class="bi bi-x"></i>
                        </button>
                    </form>
                </li>

                <!-- Dark Mode Toggle -->
                <li class="nav-item">
                    <a class="nav-link theme-toggle" href="#" onclick="toggleDarkMode(event)" title="Toggle Dark Mode">
                        <i class="bi bi-moon-fill" id="themeIcon"></i>
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged-in User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="../admin/dashboard.php"><i class="bi bi-speedometer2"></i> Admin Dashboard</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest User - Account Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="guestDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../auth/login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a></li>
                            <li><a class="dropdown-item" href="../auth/register.php">
                                <i class="bi bi-person-plus"></i> Create Account
                            </a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
// Dark Mode Toggle
function toggleDarkMode(event) {
    event.preventDefault();
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    html.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
    }
}

// Initialize theme icon on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    updateThemeIcon(currentTheme);
});

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    // Only auto-switch if user hasn't manually set a preference
    if (!localStorage.getItem('theme')) {
        const newTheme = e.matches ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme', newTheme);
        updateThemeIcon(newTheme);
    }
});

function toggleSearch(event) {
    event.preventDefault();
    const searchIcon = document.getElementById('searchIcon');
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('navbarSearchInput');

    if (searchForm.style.display === 'none') {
        // Show search form, hide icon
        searchIcon.style.display = 'none';
        searchForm.style.display = 'block';
        searchInput.focus();
    } else {
        // Show icon, hide search form
        searchForm.style.display = 'none';
        searchIcon.style.display = 'block';
        searchInput.value = '';
    }
}

// Close search if clicking outside
document.addEventListener('click', function(event) {
    const searchIcon = document.getElementById('searchIcon');
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('navbarSearchInput');

    if (searchForm.style.display !== 'none' &&
        !searchForm.contains(event.target) &&
        !searchIcon.contains(event.target)) {
        searchForm.style.display = 'none';
        searchIcon.style.display = 'block';
        searchInput.value = '';
    }
});

// Close search on ESC key
document.addEventListener('keydown', function(event) {
    const searchForm = document.getElementById('searchForm');
    const searchIcon = document.getElementById('searchIcon');

    if (event.key === 'Escape' && searchForm.style.display !== 'none') {
        searchForm.style.display = 'none';
        searchIcon.style.display = 'block';
        document.getElementById('navbarSearchInput').value = '';
    }
});
</script>
