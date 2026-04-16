<?php $__env->startSection('title','Categories'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Layout ── */
.page-grid { display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start; }
@media(max-width:900px){ .page-grid { grid-template-columns:1fr; } }

/* ── Cards ── */
.pro-card { background:#fff;border:none;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }

/* ── Category row ── */
.cat-row { display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid var(--bs-border-color);transition:background .12s;cursor:default; }
.cat-row:last-child { border-bottom:none; }
.cat-row:hover { background:rgba(13,110,253,.03); }
.cat-icon-bubble { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;background:rgba(13,110,253,.08);transition:transform .15s; }
.cat-row:hover .cat-icon-bubble { transform:scale(1.1); }
.cat-name { font-weight:600;font-size:14px;flex:1; }
.cat-count { font-size:11px;color:var(--bs-secondary-color);white-space:nowrap; }
.cat-actions { display:flex;gap:6px;flex-shrink:0; }

/* ── Emoji picker ── */
.emoji-picker { display:grid;grid-template-columns:repeat(8,1fr);gap:4px;max-height:180px;overflow-y:auto;padding:6px;background:var(--bs-tertiary-bg);border-radius:10px;border:1px solid var(--bs-border-color); }
.ep-btn { font-size:20px;padding:4px;border-radius:8px;cursor:pointer;text-align:center;border:2px solid transparent;transition:all .1s;background:transparent; }
.ep-btn:hover { background:rgba(13,110,253,.1);border-color:#0d6efd; }
.ep-btn.selected { border-color:#0d6efd;background:rgba(13,110,253,.15); }
.icon-preview { width:52px;height:52px;border-radius:12px;background:rgba(13,110,253,.08);border:2px dashed rgba(13,110,253,.3);display:flex;align-items:center;justify-content:center;font-size:26px;cursor:pointer;transition:all .15s;flex-shrink:0; }
.icon-preview:hover { border-style:solid;background:rgba(13,110,253,.12); }

/* ── Empty state ── */
.empty-state { text-align:center;padding:48px 24px;opacity:.5; }
.empty-state-icon { font-size:48px;margin-bottom:12px; }

/* ── Form card ── */
.form-section { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.5;margin-bottom:10px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">🏷️ Part Categories</h2>
        <div class="text-secondary small">Organise repair parts and accessories by brand/type</div>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size:12px;">
            <?php echo e($categories->count()); ?> categor<?php echo e($categories->count() === 1 ? 'y' : 'ies'); ?>

        </span>
    </div>
</div>

<div class="page-grid">

    
    <div>
        
        <div class="mb-3 position-relative">
            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);opacity:.4;font-size:15px;">🔍</span>
            <input type="text" id="cat-search" class="form-control ps-5" placeholder="Search categories..."
                oninput="filterCategories(this.value)" style="border-radius:12px;border:2px solid var(--bs-border-color);">
        </div>

        <div class="pro-card overflow-hidden" id="cat-list">
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $partCount = $cat->parts()->count(); ?>
            <div class="cat-row" id="cat-row-<?php echo e($cat->id); ?>" data-name="<?php echo e(strtolower($cat->name)); ?>">
                
                <div class="cat-icon-bubble"><?php echo e($cat->icon ?? '🏷️'); ?></div>

                
                <div class="flex-grow-1 min-width-0">
                    <div class="cat-name"><?php echo e($cat->name); ?></div>
                    <div class="cat-count">
                        <?php echo e($partCount); ?> part<?php echo e($partCount !== 1 ? 's' : ''); ?>

                        <?php if($partCount > 0): ?>
                        · <a href="<?php echo e(route('parts.index', ['category' => $cat->id])); ?>" class="text-decoration-none text-secondary small">View</a>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="cat-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="openEdit(<?php echo e($cat->id); ?>,'<?php echo e(addslashes($cat->name)); ?>','<?php echo e($cat->icon ?? ''); ?>')" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <?php if($partCount === 0): ?>
                    <form method="POST" action="<?php echo e(route('categories.destroy',$cat)); ?>" class="d-inline"
                        onsubmit="return confirm('Delete <?php echo e($cat->name); ?>?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete — has parts" style="opacity:.4;">
                        <i class="bi bi-trash"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🏷️</div>
                <div class="fw-semibold">No categories yet</div>
                <div class="small">Add your first category on the right</div>
            </div>
            <?php endif; ?>

            
            <div id="no-results" style="display:none;" class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <div class="fw-semibold">No matches found</div>
            </div>
        </div>
    </div>

    
    <div>
        <div class="pro-card p-4" id="form-card">

            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h5 class="syne fw-bold mb-0" id="form-title">➕ Add Category</h5>
                    <div class="text-secondary small" id="form-subtitle">Fill in the details below</div>
                </div>
                <button id="cancel-edit-btn" class="btn btn-sm btn-outline-secondary" onclick="cancelEdit()" style="display:none;">
                    Cancel
                </button>
            </div>

            <form method="POST" id="cat-form" action="<?php echo e(route('categories.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="icon" id="selected-icon" value="🏷️">
                <input type="hidden" name="color" value="#0d6efd">

                
                <div class="mb-4">
                    <div class="form-section">Icon</div>
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-preview" id="icon-preview" onclick="toggleEmojiPicker()" title="Click to pick icon">
                            🏷️
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-secondary small mb-2">Click the icon to choose an emoji, or type one:</div>
                            <input type="text" id="icon-manual" class="form-control form-control-sm"
                                placeholder="Paste emoji e.g. 📱"
                                oninput="setIconFromInput(this.value)"
                                maxlength="4" style="width:100px;font-size:20px;text-align:center;">
                        </div>
                    </div>
                    
                    <div id="emoji-picker" class="mt-3" style="display:none;">
                        <div class="form-section mb-2">Quick pick</div>
                        <div class="emoji-picker">
                            <?php $__currentLoopData = ['📱','💻','🖥️','⌚','📷','🎧','🔋','🔌','🛡️','📦','🔧','🔩','💡','🎮','📺','🖨️','⌨️','🖱️','💾','📡','🔌','🏷️','🛒','🎯','🍎','🌟','💎','🚀','🔑','🎁','💳','🏠','📞','📟','🔊','🎤','🎵','🎬','📸','🔭','🔬','⚙️','🛠️','🗜️','🔐','💰','🌐','📶','🤖']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emoji): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="ep-btn" onclick="selectEmoji('<?php echo e($emoji); ?>')"><?php echo e($emoji); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                
                <div class="mb-4">
                    <div class="form-section">Category Name *</div>
                    <input type="text" name="name" id="cat-name-input" class="form-control" required
                        placeholder="e.g. iPhone, Samsung, Screen Protectors"
                        style="border-radius:10px;font-size:14px;">
                    <div class="form-text">Use brand names (Apple, Samsung) or product types (Cases, Chargers)</div>
                </div>

                
                <div class="p-3 rounded-3 mb-4 d-flex align-items-center gap-3" style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);" id="live-preview">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(13,110,253,.08);display:flex;align-items:center;justify-content:center;font-size:22px;" id="preview-icon">🏷️</div>
                    <div>
                        <div class="fw-semibold" id="preview-name" style="font-size:14px;">Category name</div>
                        <div class="text-secondary small">0 parts</div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" id="form-submit-btn"
                    style="border-radius:12px;font-size:15px;">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span id="submit-label">Add Category</span>
                </button>
            </form>
        </div>

        
        <div class="pro-card p-4 mt-3">
            <div class="form-section">💡 Tips</div>
            <ul class="list-unstyled mb-0" style="font-size:12px;color:var(--bs-secondary-color);">
                <li class="mb-2">🏷️ Use <strong>brand names</strong> like iPhone, Samsung, OnePlus for phone-specific parts</li>
                <li class="mb-2">📦 Use <strong>product types</strong> like Cases, Chargers, Screen Protectors for accessories</li>
                <li class="mb-2">🔧 Categories appear in the <strong>Parts</strong> and <strong>POS</strong> drill-down</li>
                <li>🗑️ Categories with parts <strong>cannot be deleted</strong> — reassign parts first</li>
            </ul>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
var currentEditId = null;
var currentIcon   = '🏷️';

// ── Live preview ──────────────────────────────────────────────────
document.getElementById('cat-name-input').addEventListener('input', function() {
    document.getElementById('preview-name').textContent = this.value || 'Category name';
});

// ── Search filter ─────────────────────────────────────────────────
function filterCategories(q) {
    var rows    = document.querySelectorAll('.cat-row');
    var noRes   = document.getElementById('no-results');
    var visible = 0;
    rows.forEach(function(row) {
        var match = row.dataset.name.includes(q.toLowerCase());
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    noRes.style.display = (visible === 0 && q.length > 0) ? '' : 'none';
}

// ── Emoji picker ──────────────────────────────────────────────────
function toggleEmojiPicker() {
    var picker = document.getElementById('emoji-picker');
    picker.style.display = picker.style.display === 'none' ? '' : 'none';
}

function selectEmoji(emoji) {
    setIcon(emoji);
    document.getElementById('emoji-picker').style.display = 'none';
    // Highlight selected
    document.querySelectorAll('.ep-btn').forEach(function(b){
        b.classList.toggle('selected', b.textContent === emoji);
    });
}

function setIconFromInput(val) {
    var emoji = val.trim();
    if (emoji) setIcon(emoji);
}

function setIcon(emoji) {
    currentIcon = emoji;
    document.getElementById('icon-preview').textContent   = emoji;
    document.getElementById('preview-icon').textContent   = emoji;
    document.getElementById('selected-icon').value        = emoji;
    document.getElementById('icon-manual').value          = emoji;
}

// ── Open edit mode ────────────────────────────────────────────────
function openEdit(id, name, icon) {
    currentEditId = id;

    // Update form
    document.getElementById('cat-form').action    = '/categories/' + id;
    document.getElementById('form-method').value  = 'PUT';
    document.getElementById('cat-name-input').value = name;
    setIcon(icon || '🏷️');

    // Update UI
    document.getElementById('form-title').textContent    = '✏️ Edit Category';
    document.getElementById('form-subtitle').textContent = 'Update the details below';
    document.getElementById('submit-label').textContent  = 'Update Category';
    document.getElementById('cancel-edit-btn').style.display = '';
    document.getElementById('preview-name').textContent  = name;

    // Highlight selected row
    document.querySelectorAll('.cat-row').forEach(function(r){ r.style.background = ''; });
    var row = document.getElementById('cat-row-' + id);
    if (row) row.style.background = 'rgba(13,110,253,.06)';

    // Scroll to form on mobile
    document.getElementById('form-card').scrollIntoView({ behavior:'smooth', block:'nearest' });

    // Focus name input
    setTimeout(function(){ document.getElementById('cat-name-input').focus(); }, 300);
}

// ── Cancel edit ───────────────────────────────────────────────────
function cancelEdit() {
    currentEditId = null;
    document.getElementById('cat-form').action    = '<?php echo e(route("categories.store")); ?>';
    document.getElementById('form-method').value  = 'POST';
    document.getElementById('cat-name-input').value = '';
    setIcon('🏷️');
    document.getElementById('form-title').textContent    = '➕ Add Category';
    document.getElementById('form-subtitle').textContent = 'Fill in the details below';
    document.getElementById('submit-label').textContent  = 'Add Category';
    document.getElementById('cancel-edit-btn').style.display = 'none';
    document.getElementById('preview-name').textContent  = 'Category name';
    document.querySelectorAll('.cat-row').forEach(function(r){ r.style.background = ''; });
}

// Close emoji picker when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#emoji-picker') && !e.target.closest('.icon-preview')) {
        var picker = document.getElementById('emoji-picker');
        if (picker) picker.style.display = 'none';
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/categories/index.blade.php ENDPATH**/ ?>