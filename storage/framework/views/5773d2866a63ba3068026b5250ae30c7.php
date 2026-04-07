<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Mobile Shop'); ?> — Repair Tracker</title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <?php echo $__env->yieldPushContent('styles'); ?>

    <style>
        /* ── Page Loader ─────────────────────────────────────────── */
        #page-loader {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 16px;
            background: var(--bs-body-bg);
            transition: opacity .35s ease;
        }
        #page-loader.fade-out { opacity: 0; pointer-events: none; }
        .loader-spinner {
            width: 48px; height: 48px; border-radius: 50%;
            border: 4px solid var(--bs-border-color);
            border-top-color: #0d6efd;
            animation: spin .75s linear infinite;
        }
        .loader-text { font-family: 'Syne', sans-serif; font-size: 14px; color: var(--bs-secondary-color); letter-spacing: .05em; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Fonts ───────────────────────────────────────────────── */
        body { font-family: 'DM Sans', sans-serif; }
        h1,h2,h3,h4,.syne { font-family: 'Syne', sans-serif; }

        /* ── Sidebar ─────────────────────────────────────────────── */
        #sidebar {
            width: 230px; min-height: 100vh;
            position: fixed; top: 0; left: 0; bottom: 0; z-index: 1040;
            display: flex; flex-direction: column;
            background: var(--bs-body-bg);
            border-right: 1px solid var(--bs-border-color);
            overflow-y: auto; overflow-x: hidden;
            transition: transform .25s ease, width .25s ease;
        }
        #sidebar.collapsed { transform: translateX(-100%); }
        .sidebar-logo {
            padding: 16px 14px 12px;
            border-bottom: 1px solid var(--bs-border-color);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-logo h5 { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 800; margin: 0; line-height: 1.2; }
        .sidebar-logo small { font-size: 10px; opacity: .6; }
        .nav-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; opacity: .5;
            padding: 10px 14px 3px;
        }
        #sidebar .nav-link {
            font-size: 13px; font-weight: 500; padding: 7px 12px;
            border-radius: 7px; margin: 1px 6px;
            color: var(--bs-secondary-color);
            display: flex; align-items: center; gap: 9px;
            transition: all .15s;
        }
        #sidebar .nav-link:hover { background: var(--bs-secondary-bg); color: var(--bs-body-color); }
        #sidebar .nav-link.active { background: #0d6efd; color: #fff !important; }
        #sidebar .nav-link .nav-icon { font-size: 14px; width: 18px; text-align: center; flex-shrink: 0; }

        /* ── Topbar ──────────────────────────────────────────────── */
        #topbar {
            height: 54px; position: sticky; top: 0; z-index: 1030;
            margin-left: 230px;
            background: var(--bs-body-bg);
            border-bottom: 1px solid var(--bs-border-color);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 16px; gap: 10px;
            transition: margin-left .25s ease;
        }
        #topbar.full-width { margin-left: 0; }
        .topbar-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; }

        /* ── Main content ────────────────────────────────────────── */
        #main-content {
            margin-left: 230px;
            padding: 20px;
            min-height: 100vh;
            transition: margin-left .25s ease;
            background: #f0f2f5;
        }
        [data-bs-theme="dark"] #main-content { background: #0a0a0c; }
        #main-content.full-width { margin-left: 0; }

        /* ── Page header ─────────────────────────────────────────── */
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-wrap: wrap; gap: 10px; margin-bottom: 20px;
        }
        .page-header h2 { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; margin: 0; }
        .page-header p  { font-size: 13px; opacity: .6; margin: 2px 0 0; }

        /* ── Stat cards ──────────────────────────────────────────── */
        .stat-card .stat-value { font-family: 'Syne', sans-serif; font-size: 28px; font-weight: 800; }
        .stat-card .stat-label { font-size: 11px; opacity: .6; }
        .stat-card .stat-icon  { font-size: 24px; }

        /* ── Tables ──────────────────────────────────────────────── */
        .table { font-size: 13px; }
        .table thead th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }

        /* ── Form helpers ────────────────────────────────────────── */
        .form-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; opacity: .7; margin-bottom: 4px; }
        .form-grid  { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }
        .form-grid .full { grid-column: 1/-1; }

        /* ── Filter bar ──────────────────────────────────────────── */
        .filter-bar { padding: 14px 16px; border-radius: 10px; margin-bottom: 18px; }

        /* ── Empty state ─────────────────────────────────────────── */
        .empty-state { text-align: center; padding: 48px 20px; opacity: .5; }
        .empty-state .empty-icon { font-size: 40px; margin-bottom: 10px; }

        /* ── Tom Select dark/light ───────────────────────────────── */
        .ts-control, .ts-dropdown {
            background-color: var(--bs-tertiary-bg) !important;
            border-color: var(--bs-border-color) !important;
            color: var(--bs-body-color) !important;
        }
        .ts-dropdown .option { color: var(--bs-body-color); }
        .ts-dropdown .option:hover, .ts-dropdown .option.active { background: #0d6efd !important; color: #fff !important; }
        .ts-dropdown .create { padding: 8px 12px; font-size: 13px; color: #0d6efd; cursor: pointer; border-top: 1px solid var(--bs-border-color); }
        .ts-dropdown .create:hover { background: #0d6efd !important; color: #fff !important; }
        .ts-dropdown .no-results { padding: 8px 12px; font-size: 13px; opacity: .6; }
        .ts-wrapper.multi .ts-control > .item { background: #0d6efd; color: #fff; border-radius: 4px; }
        [data-bs-theme="light"] .ts-control, [data-bs-theme="light"] .ts-dropdown { background-color: #fff !important; }

        /* ── Theme toggle btn ────────────────────────────────────── */
        #theme-toggle { border: none; background: none; font-size: 18px; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: background .15s; color: var(--bs-secondary-color); }
        #theme-toggle:hover { background: var(--bs-secondary-bg); }

        /* ── Mobile ──────────────────────────────────────────────── */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
                width: 260px;
                z-index: 1050;
                box-shadow: 4px 0 20px rgba(0,0,0,.3);
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #topbar { margin-left: 0 !important; }
            #main-content { margin-left: 0 !important; padding: 12px; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1049; }
            .sidebar-overlay.active { display: block !important; }
        }
        /* Desktop: overlay NEVER shows */
        @media (min-width: 769px) {
            .sidebar-overlay { display: none !important; }
        }
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 1039;
        }

        /* ── Misc ────────────────────────────────────────────────── */
        .fw-bold { font-weight: 600 !important; }
        .card { border-radius: 10px; }
        .card-header { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; }
        code { font-size: 12px; padding: 1px 5px; border-radius: 3px; }
        .text-green { color: #198754 !important; }
        .text-red   { color: #dc3545 !important; }
    </style>
</head>
<body>


<div id="page-loader">
    <div class="loader-spinner"></div>
    <div class="loader-text">📱 Loading...</div>
</div>


<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>


<aside id="sidebar">
    <div class="sidebar-logo">
        <span style="font-size:26px;">📱</span>
        <div>
            <h5>Mobile Shop</h5>
            <small>Repair Tracker</small>
        </div>
    </div>

    <nav class="flex-column pt-1 pb-2">
        <?php $u = auth()->user(); ?>

        <div class="nav-section-label">Main</div>
        <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
            <span class="nav-icon">🏠</span> Dashboard
        </a>

        <?php if($u->hasPermission('parts.view')): ?>
        <div class="nav-section-label mt-1">Stock</div>
        <?php if($u->hasPermission('repair-types.manage')): ?>
        <a href="<?php echo e(route('categories.index')); ?>" class="nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
            <span class="nav-icon">🏷️</span> Brands
        </a>
        <?php endif; ?>
        <a href="<?php echo e(route('parts.index')); ?>" class="nav-link <?php echo e(request()->routeIs('parts.*') ? 'active' : ''); ?>">
            <span class="nav-icon">🗃️</span> Repair Stock
        </a>
        <?php endif; ?>

        <div class="nav-section-label mt-1">Repairs</div>
        <?php if($u->hasPermission('repair-types.manage')): ?>
        <a href="<?php echo e(route('repair-types.index')); ?>" class="nav-link <?php echo e(request()->routeIs('repair-types.*') ? 'active' : ''); ?>">
            <span class="nav-icon">🔩</span> Repair Types
        </a>
        <?php endif; ?>
        <?php if($u->hasPermission('customers.view')): ?>
        <a href="<?php echo e(route('customers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
            <span class="nav-icon">👥</span> Customers
        </a>
        <?php endif; ?>
        <?php if($u->hasPermission('jobs.view')): ?>
        <a href="<?php echo e(route('jobs.index')); ?>" class="nav-link <?php echo e(request()->routeIs('jobs.*') ? 'active' : ''); ?>">
            <span class="nav-icon">🔧</span> Jobs
        </a>
        <?php endif; ?>

        <?php if($u->hasPermission('phone-deals.view')): ?>
        <div class="nav-section-label mt-1">Deals</div>
        <a href="<?php echo e(route('phone-deals.index')); ?>" class="nav-link <?php echo e(request()->routeIs('phone-deals.*') ? 'active' : ''); ?>">
            <span class="nav-icon">📲</span> Phone Deals
        </a>
        <a href="<?php echo e(route('inventory.index')); ?>" class="nav-link <?php echo e(request()->routeIs('inventory.*') ? 'active' : ''); ?>">
            <span class="nav-icon">📦</span> Inventory
        </a>
        <?php if($u->hasPermission('repair-types.manage')): ?>
        <a href="<?php echo e(route('device-categories.index')); ?>" class="nav-link <?php echo e(request()->routeIs('device-categories.*') ? 'active' : ''); ?>">
            <span class="nav-icon">🏷️</span> Device Types
        </a>
        <?php endif; ?>
        <a href="<?php echo e(route('terms.index')); ?>" class="nav-link <?php echo e(request()->routeIs('terms.*') ? 'active' : ''); ?>">
            <span class="nav-icon">📄</span> Terms & Conditions
        </a>
        <?php endif; ?>

        <?php if($u->hasPermission('vouchers.view')): ?>
        <a href="<?php echo e(route('vouchers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('vouchers.*') ? 'active' : ''); ?>">
            <span class="nav-icon">🎟️</span> Vouchers
        </a>
        <?php endif; ?>

        <div class="nav-section-label mt-1">System</div>
        <?php if($u->hasPermission('users.view')): ?>
        <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
            <span class="nav-icon">👤</span> Users
        </a>
        <?php endif; ?>
        <?php if($u->hasPermission('settings.view')): ?>
        <a href="<?php echo e(route('settings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
            <span class="nav-icon">⚙️</span> Settings
        </a>
        <?php endif; ?>
    </nav>

    <div class="mt-auto px-3 py-2 border-top" style="font-size:10px;opacity:.4;text-align:center;">Mobile Shop v1.0</div>
</aside>


<div id="topbar">
    <div class="d-flex align-items-center gap-2">
        <button onclick="toggleSidebar()" class="btn btn-sm btn-outline-secondary border-0" title="Toggle sidebar">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="topbar-title"><?php echo $__env->yieldContent('title','Dashboard'); ?></span>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <?php echo $__env->yieldContent('topbar-actions'); ?>

        
        <button id="theme-toggle" title="Toggle dark/light mode">🌙</button>

        
        <div class="vr mx-1"></div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold" style="width:28px;height:28px;font-size:12px;">
                    <?php echo e(strtoupper(substr(auth()->user()->name,0,1))); ?>

                </span>
                <span class="d-none d-md-inline" style="font-size:13px;"><?php echo e(auth()->user()->name); ?></span>
                <i class="bi bi-chevron-down" style="font-size:10px;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text small text-muted"><?php echo e(auth()->user()->email); ?></span></li>
                <li><span class="dropdown-item-text small"><span class="badge bg-primary"><?php echo e(ucfirst(auth()->user()->role)); ?></span></span></li>
                <li><hr class="dropdown-divider"></li>
                <?php if(auth()->user()->hasPermission('users.view')): ?>
                <li><a class="dropdown-item" href="<?php echo e(route('users.index')); ?>"><i class="bi bi-people me-2"></i>Manage Users</a></li>
                <?php endif; ?>
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>


<div id="main-content">

    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <div><?php echo e(session('success')); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div><?php echo e(session('error')); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div><?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>

<script>
// ── Page loader ───────────────────────────────────────────────────
function hidePageLoader() {
    var loader = document.getElementById('page-loader');
    if (!loader) return;
    loader.classList.add('fade-out');
    setTimeout(function () { loader.style.display = 'none'; }, 400);
}
// Hide on load (normal)
window.addEventListener('load', hidePageLoader);
// Hide on DOMContentLoaded as fallback (catches stuck loaders)
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(hidePageLoader, 500);
    // Always ensure overlay is hidden on desktop
    if (window.innerWidth >= 769) {
        var overlay = document.getElementById('sidebar-overlay');
        if (overlay) {
            overlay.classList.remove('active');
            overlay.style.display = 'none';
        }
    }
});
// Hard fallback — always hide after 3 seconds no matter what
setTimeout(hidePageLoader, 3000);

// ── Dark / Light mode ─────────────────────────────────────────────
var html        = document.documentElement;
var themeToggle = document.getElementById('theme-toggle');
var saved       = localStorage.getItem('theme') || 'dark';

function applyTheme(t) {
    html.setAttribute('data-bs-theme', t);
    themeToggle.textContent = t === 'dark' ? '🌙' : '☀️';
    localStorage.setItem('theme', t);
}
applyTheme(saved);
themeToggle.addEventListener('click', function () {
    applyTheme(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
});

// ── Sidebar toggle ────────────────────────────────────────────────
var sidebar     = document.getElementById('sidebar');
var topbar      = document.getElementById('topbar');
var mainContent = document.getElementById('main-content');
var overlay     = document.getElementById('sidebar-overlay');
var isMobile    = function() { return window.innerWidth <= 768; };

function toggleSidebar() {
    if (isMobile()) {
        var isOpen = sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active', isOpen);
    } else {
        var c = sidebar.classList.toggle('collapsed');
        topbar.classList.toggle('full-width', c);
        mainContent.classList.toggle('full-width', c);
        localStorage.setItem('sidebar_collapsed', c ? '1' : '0');
    }
}

function closeSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
}

// Close sidebar when a nav link is clicked on mobile
document.querySelectorAll('#sidebar .nav-link').forEach(function(link) {
    link.addEventListener('click', function() {
        if (isMobile()) { closeSidebar(); }
    });
});

// Close sidebar on window resize if switching to desktop
window.addEventListener('resize', function() {
    if (!isMobile()) { closeSidebar(); }
});

// Restore desktop collapse state
(function () {
    if (!isMobile() && localStorage.getItem('sidebar_collapsed') === '1') {
        sidebar.classList.add('collapsed');
        topbar.classList.add('full-width');
        mainContent.classList.add('full-width');
    }
})();

// ── Tom Select ────────────────────────────────────────────────────
function initTomSelectEl(el) {
    if (el.tomselect || el.closest('template')) return;
    var isTaggable = el.classList.contains('repair-type-select') || el.classList.contains('part-select');
    new TomSelect(el, {
        allowEmptyOption: !isTaggable,
        maxOptions: 300,
        plugins: el.multiple ? ['remove_button'] : [],
        create: isTaggable ? function (input) { return { value: input, text: input }; } : false,
        createOnBlur: false,
        persist: false,
        placeholder: isTaggable ? 'Search or type & press Enter...' : '',
        onItemAdd: function () { this.setTextboxValue(''); this.refreshOptions(false); },
        onChange: function(value) {
            // Set value on underlying select then dispatch change
            el.value = value;
            el.dispatchEvent(new Event('change', { bubbles: true }));
        },
        render: isTaggable ? {
            option_create: function (data, escape) {
                return '<div class="create">Add <strong>' + escape(data.input) + '</strong> ↵</div>';
            },
            no_results: function (data, escape) {
                return '<div class="no-results">No results — press Enter to add <strong>' + escape(data.input) + '</strong></div>';
            }
        } : {}
    });
}
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('select:not(.no-ts)').forEach(function (el) {
        if (!el.closest('template')) initTomSelectEl(el);
    });
});
window.initTomSelect = function (container) {
    container.querySelectorAll('select:not(.no-ts)').forEach(function (el) { initTomSelectEl(el); });
};
</script>
</body>
</html><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/layouts/app.blade.php ENDPATH**/ ?>