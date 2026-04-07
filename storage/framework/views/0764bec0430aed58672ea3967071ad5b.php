<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Mobile Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .syne { font-family: 'Syne', sans-serif; }
        #page-loader {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 14px;
            background: var(--bs-body-bg); transition: opacity .3s ease;
        }
        #page-loader.fade-out { opacity: 0; pointer-events: none; }
        .loader-spinner { width: 42px; height: 42px; border-radius: 50%; border: 4px solid var(--bs-border-color); border-top-color: #0d6efd; animation: spin .75s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        #theme-toggle { background: none; border: none; cursor: pointer; font-size: 20px; position: fixed; top: 16px; right: 16px; padding: 6px; border-radius: 8px; }
        #theme-toggle:hover { background: var(--bs-secondary-bg); }
    </style>
</head>
<body>

<div id="page-loader">
    <div class="loader-spinner"></div>
    <div class="text-secondary" style="font-size:13px;">📱 Loading...</div>
</div>

<button id="theme-toggle" title="Toggle theme">🌙</button>

<div class="container" style="max-width:420px;">
    <div class="text-center mb-4">
        <div style="font-size:52px; line-height:1;">📱</div>
        <h1 class="syne fw-bold mt-2 mb-1" style="font-size:22px;">Mobile Shop</h1>
        <p class="text-secondary small">Repair Tracker System</p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h5 class="syne fw-bold mb-4"><i class="bi bi-lock me-2"></i>Sign In</h5>

            <?php if($errors->any()): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div><?php echo e($errors->first()); ?></div>
            </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div><?php echo e(session('error')); ?></div>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary text-uppercase" style="letter-spacing:.04em;font-size:11px;">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required autofocus placeholder="admin@mobileshop.com">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary text-uppercase" style="letter-spacing:.04em;font-size:11px;">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="pwd" required placeholder="••••••••">
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd()" tabindex="-1">
                            <i class="bi bi-eye" id="pwd-icon"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3 d-flex align-items-center gap-2">
                    <input class="form-check-input mt-0" type="checkbox" name="remember" value="1" id="remember">
                    <label class="form-check-label small" for="remember">Keep me signed in</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    Sign In <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>

            <div class="text-center mt-3">
                <small class="text-secondary opacity-50">Default: admin@mobileshop.com / password</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('load', function () {
    var l = document.getElementById('page-loader');
    l.classList.add('fade-out');
    setTimeout(function () { l.style.display = 'none'; }, 350);
});

var html  = document.documentElement;
var saved = localStorage.getItem('theme') || 'dark';
var btn   = document.getElementById('theme-toggle');
function applyTheme(t) { html.setAttribute('data-bs-theme', t); btn.textContent = t === 'dark' ? '🌙' : '☀️'; localStorage.setItem('theme', t); }
applyTheme(saved);
btn.addEventListener('click', function () { applyTheme(html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark'); });

function togglePwd() {
    var p = document.getElementById('pwd');
    var i = document.getElementById('pwd-icon');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
<?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/auth/login.blade.php ENDPATH**/ ?>