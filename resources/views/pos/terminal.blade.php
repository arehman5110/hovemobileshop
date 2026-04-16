@extends('layouts.app')
@section('title','POS')

@push('styles')
<style>
/* ── Shell ── */
.pos-shell {
    position:fixed;inset:54px 0 0 230px;
    display:grid;grid-template-columns:1fr 340px;
    overflow:hidden;
    background:var(--bs-tertiary-bg);
}
@media(max-width:991px){
    .pos-shell{left:0;grid-template-columns:1fr;position:relative;inset:auto;min-height:100vh;}
}

/* ── Left panel ── */
.pos-l {display:flex;flex-direction:column;overflow:hidden;}

.pos-bar {
    padding:10px 14px;
    background:var(--bs-body-bg);
    border-bottom:1px solid var(--bs-border-color);
    display:flex;align-items:center;gap:8px;flex-shrink:0;
}

.pos-grid-area {
    flex:1;overflow-y:auto;
    padding:16px;
    background:var(--bs-tertiary-bg);
}

/* ── Breadcrumb ── */
.bcc {display:flex;align-items:center;gap:4px;flex:1;min-width:0;font-size:12px;}
.bcc-link {
    color:var(--bs-primary);cursor:pointer;padding:3px 8px;
    border-radius:6px;font-weight:600;white-space:nowrap;
    transition:background .1s;
}
.bcc-link:hover {background:rgba(var(--bs-primary-rgb),.1);}
.bcc-sep {color:var(--bs-border-color);font-size:10px;}
.bcc-cur {font-weight:700;color:var(--bs-body-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* ── Search ── */
.srch-wrap {position:relative;width:200px;flex-shrink:0;}
.srch-wrap input {
    width:100%;height:34px;padding:0 10px 0 32px;
    border-radius:8px;border:1.5px solid var(--bs-border-color);
    background:var(--bs-tertiary-bg);color:var(--bs-body-color);font-size:12px;outline:none;
    transition:border-color .15s,background .15s;
}
.srch-wrap input:focus {border-color:var(--bs-primary);background:var(--bs-body-bg);}
.srch-si {position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;opacity:.4;pointer-events:none;}
.sr-drop {
    position:absolute;top:calc(100%+5px);left:0;right:0;
    background:var(--bs-body-bg);border:1px solid var(--bs-border-color);
    border-radius:12px;z-index:600;max-height:260px;overflow-y:auto;
    box-shadow:0 12px 32px rgba(0,0,0,.12);display:none;
}
.sr-row {
    display:flex;align-items:center;gap:10px;padding:9px 12px;
    cursor:pointer;border-bottom:1px solid var(--bs-border-color);transition:background .1s;
}
.sr-row:last-child {border-bottom:none;}
.sr-row:hover {background:var(--bs-tertiary-bg);}
.sr-icon {
    width:32px;height:32px;border-radius:8px;
    background:rgba(var(--bs-primary-rgb),.1);
    display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;
}
.sr-nm {font-size:12px;font-weight:600;flex:1;color:var(--bs-body-color);line-height:1.3;}
.sr-meta {font-size:10px;color:var(--bs-secondary-color);}
.sr-pr {font-size:13px;font-weight:700;color:var(--bs-primary);white-space:nowrap;}

/* ── Section heading ── */
.grid-hdr {
    font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
    color:var(--bs-secondary-color);margin-bottom:14px;
    display:flex;align-items:center;gap:10px;
}
.grid-hdr::after {content:"";flex:1;height:1px;background:var(--bs-border-color);}

/* ── Product cards ── */
.pg {display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;}

.pc {
    background:var(--bs-body-bg);
    border:none;
    border-radius:16px;
    padding:18px 12px 14px;
    cursor:pointer;text-align:center;user-select:none;
    transition:transform .14s,box-shadow .14s;
    position:relative;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
    aspect-ratio:1 / 1.1;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
}
.pc:hover {
    transform:translateY(-3px);
    box-shadow:0 8px 24px rgba(var(--bs-primary-rgb),.18);
}
.pc:active {transform:scale(.96);}
.pc.flash {
    border-color:#10b981;
    box-shadow:0 0 0 4px rgba(16,185,129,.15);
}
.pc.oos {opacity:.4;cursor:not-allowed;}
.pc.oos:hover {transform:none;box-shadow:0 1px 4px rgba(0,0,0,.06);border-color:var(--bs-border-color);}

.pc-ico {font-size:30px;margin-bottom:10px;display:block;line-height:1;}
.pc-nm {font-size:12px;font-weight:700;line-height:1.35;margin-bottom:4px;color:var(--bs-body-color);}
.pc-var {
    display:inline-block;font-size:10px;font-weight:600;
    padding:2px 8px;border-radius:20px;margin-bottom:6px;
    background:rgba(var(--bs-primary-rgb),.1);color:var(--bs-primary);
}
.pc-pr {
    font-size:19px;font-weight:800;color:var(--bs-primary);
    font-family:'Syne',sans-serif;letter-spacing:-.3px;
}
.pc-st {font-size:10px;color:var(--bs-secondary-color);margin-top:4px;}
.pc-st.low {color:#f59e0b;font-weight:700;}

/* ── RIGHT panel ── */
.pos-r {
    display:flex;flex-direction:column;
    background:var(--bs-tertiary-bg);
    border-left:none;
}

/* Order card */
.order-card {
    margin:12px;
    background:var(--bs-body-bg);
    border:none;
    border-radius:16px;
    box-shadow:0 2px 16px rgba(0,0,0,.08);
    flex-shrink:0;
    overflow:hidden;
}

.cart-top {
    padding:12px 14px;
    border-bottom:1px solid var(--bs-border-color);
    display:flex;align-items:center;justify-content:space-between;
    background:var(--bs-tertiary-bg);
    border-radius:16px 16px 0 0;
}
.cart-title {font-size:13px;font-weight:700;color:var(--bs-body-color);}
.cart-count-pill {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:20px;height:20px;border-radius:10px;
    background:var(--bs-primary);color:#fff;
    font-size:10px;font-weight:700;padding:0 5px;margin-left:6px;
}

/* Customer selector */
.cart-cust {
    padding:8px 12px;
    border-bottom:1px solid var(--bs-border-color);
    background:var(--bs-body-bg);
}
.cart-cust select {
    width:100%;height:32px;border:1.5px solid var(--bs-border-color);
    border-radius:8px;background:var(--bs-tertiary-bg);
    color:var(--bs-body-color);font-size:11px;padding:0 10px;outline:none;
    transition:border-color .12s;
}
.cart-cust select:focus {border-color:var(--bs-primary);}

/* Items */
.cart-items {flex:1;overflow-y:auto;padding:6px;min-height:0;}
.cart-empty {
    height:100%;display:flex;flex-direction:column;
    align-items:center;justify-content:center;
    gap:6px;opacity:.35;
}
.ci {
    display:flex;align-items:center;gap:8px;
    padding:8px 8px;border-radius:10px;transition:background .1s;
}
.ci:hover {background:var(--bs-tertiary-bg);}
.ci-inf {flex:1;min-width:0;}
.ci-nm {font-size:12px;font-weight:700;color:var(--bs-body-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.ci-sb {font-size:10px;color:var(--bs-secondary-color);}
.ci-tot {font-size:13px;font-weight:800;color:var(--bs-primary);white-space:nowrap;min-width:46px;text-align:right;}
.qctrl {display:flex;align-items:center;gap:3px;flex-shrink:0;}
.qb {
    width:22px;height:22px;border-radius:7px;
    border:1.5px solid var(--bs-border-color);
    background:var(--bs-tertiary-bg);
    cursor:pointer;font-size:13px;font-weight:700;
    color:var(--bs-primary);display:flex;align-items:center;justify-content:center;
    transition:all .1s;
}
.qb:hover {background:var(--bs-primary);border-color:var(--bs-primary);color:#fff;}
.qn {font-size:12px;font-weight:700;min-width:20px;text-align:center;color:var(--bs-body-color);}
.xb {
    width:18px;height:18px;border:none;background:transparent;
    cursor:pointer;color:var(--bs-secondary-color);font-size:11px;
    display:flex;align-items:center;justify-content:center;border-radius:4px;padding:0;
}
.xb:hover {background:rgba(220,53,69,.12);color:#dc3545;}

/* ── Footer ── */
.cart-foot {
    padding:12px 14px 14px;
    border-top:1px solid var(--bs-border-color);
    background:var(--bs-body-bg);
    border-radius:0 0 16px 16px;
}
.tl {display:flex;justify-content:space-between;align-items:center;font-size:12px;margin-bottom:4px;color:var(--bs-secondary-color);}
.tl.grand {
    font-size:24px;font-weight:800;font-family:'Syne',sans-serif;
    color:#10b981;margin:8px 0 12px;letter-spacing:-.4px;
}
/* Discount + Charge in one row */
.action-row {display:flex;gap:8px;align-items:center;}
.btn-disc-ico {
    width:42px;height:42px;border-radius:10px;flex-shrink:0;
    border:none;
    background:var(--bs-tertiary-bg);
    color:var(--bs-secondary-color);font-size:18px;
    cursor:pointer;display:flex;align-items:center;justify-content:center;
    transition:all .12s;position:relative;
    box-shadow:0 1px 4px rgba(0,0,0,.07);
}
.btn-disc-ico:hover,.btn-disc-ico.on {
    color:#f59e0b;background:rgba(245,158,11,.1);
}
.disc-dot {
    position:absolute;top:-3px;right:-3px;
    width:10px;height:10px;border-radius:5px;
    background:#f59e0b;border:2px solid var(--bs-body-bg);
    display:none;
}
.btn-disc-ico.on .disc-dot {display:block;}
.btn-charge {
    flex:1;height:42px;font-size:14px;font-weight:700;
    border:none;border-radius:10px;cursor:pointer;
    background:#10b981;color:#fff;letter-spacing:-.2px;
    transition:filter .15s,transform .1s;
    box-shadow:0 3px 12px rgba(16,185,129,.35);
}
.btn-charge:hover {filter:brightness(1.08);transform:translateY(-1px);}
.btn-charge:active {transform:scale(.98);}
.btn-charge:disabled {opacity:.35;cursor:not-allowed;filter:none;transform:none;box-shadow:none;}

/* ── Pay modal ── */
.pm-pill {
    flex:1;padding:10px 6px;
    border:1.5px solid var(--bs-border-color);
    border-radius:12px;cursor:pointer;text-align:center;
    background:var(--bs-tertiary-bg);transition:all .12s;
}
.pm-pill.on {border-color:#10b981;background:rgba(16,185,129,.08);}
.pm-ico {font-size:22px;margin-bottom:3px;}
.pm-lbl {font-size:10px;font-weight:700;color:var(--bs-secondary-color);}
.pm-pill.on .pm-lbl {color:#10b981;}
.cash-tile {
    flex:1;min-width:0;padding:9px;border-radius:10px;
    border:1.5px solid var(--bs-border-color);
    background:var(--bs-tertiary-bg);
    color:var(--bs-body-color);font-weight:700;font-size:14px;cursor:pointer;
    transition:all .12s;
}
.cash-tile:hover {border-color:var(--bs-primary);color:var(--bs-primary);background:rgba(var(--bs-primary-rgb),.07);}
.cash-tile.exact {border-color:rgba(var(--bs-primary-rgb),.3);color:var(--bs-primary);}
.chg-box {
    background:rgba(16,185,129,.07);border:1.5px solid rgba(16,185,129,.2);
    border-radius:12px;padding:12px 16px;text-align:center;margin-top:12px;
}

/* ── Back btn ── */
.back-btn {
    width:32px;height:32px;border-radius:8px;flex-shrink:0;
    border:1.5px solid var(--bs-border-color);
    background:var(--bs-tertiary-bg);
    color:var(--bs-body-color);cursor:pointer;font-size:15px;font-weight:700;
    display:flex;align-items:center;justify-content:center;transition:all .1s;
}
.back-btn:hover {border-color:var(--bs-primary);color:var(--bs-primary);}
</style>
@endpush

@section('content')
<div class="pos-shell">

{{-- LEFT --}}
<div class="pos-l">
    <div class="pos-bar">
        <button id="back-btn" onclick="goBack()" class="back-btn" style="display:none;">&#8592;</button>
        <div class="bcc" id="breadcrumb"><span class="bcc-cur">Categories</span></div>
        <div class="srch-wrap">
            <span class="srch-si">&#9906;</span>
            <input type="text" id="srch" placeholder="Search..." autocomplete="off">
            <div class="sr-drop" id="sr-drop"></div>
        </div>
    </div>

    <div class="pos-grid-area" id="grid-area">
        <div style="height:180px;display:flex;align-items:center;justify-content:center;opacity:.35;">
            <span style="font-size:13px;color:var(--bs-secondary-color);">Loading...</span>
        </div>
    </div>
</div>

{{-- RIGHT --}}
<div class="pos-r">

    <div class="order-card" style="flex:1;display:flex;flex-direction:column;overflow:hidden;">

        <div class="cart-top">
            <div class="cart-title">
                Your Order
                <span class="cart-count-pill" id="c-count">0</span>
            </div>
            <div style="display:flex;gap:6px;">
                <button onclick="openCustom()" style="padding:4px 10px;border-radius:7px;border:1.5px solid var(--bs-border-color);background:var(--bs-tertiary-bg);color:var(--bs-secondary-color);font-size:11px;font-weight:600;cursor:pointer;transition:all .1s;" onmouseover="this.style.color='var(--bs-primary)'" onmouseout="this.style.color='var(--bs-secondary-color)'">+ Custom</button>
                <button onclick="clearCart()" style="padding:4px 10px;border-radius:7px;border:1.5px solid rgba(220,53,69,.2);background:rgba(220,53,69,.05);color:#dc3545;font-size:11px;font-weight:600;cursor:pointer;transition:all .1s;">Clear</button>
            </div>
        </div>

        <div class="cart-cust">
            <select id="cust-sel" class="no-ts">
                <option value="">Walk-in customer</option>
                @foreach($customers as $c)
                <option value="{{ $c->id }}">{{ $c->name }}{{ $c->phone ? ' · '.$c->phone : '' }}</option>
                @endforeach
            </select>
        </div>

        <div class="cart-items" id="cart-body">
            <div class="cart-empty" id="c-empty">
                <div style="font-size:32px;opacity:.5;">&#128722;</div>
                <div style="font-size:13px;font-weight:700;color:var(--bs-secondary-color);">Cart is empty</div>
                <div style="font-size:11px;color:var(--bs-secondary-color);">Tap a product to add</div>
            </div>
        </div>

        <div class="cart-foot">
            <div class="tl"><span>Subtotal</span><span id="t-sub">£0.00</span></div>
            <div class="tl" id="t-disc-row" style="display:none;">
                <span id="t-disc-lbl">Discount</span>
                <span style="color:#dc3545;" id="t-disc-val">-£0.00</span>
            </div>
            <div class="tl grand"><span>Total</span><span id="t-total">£0.00</span></div>
            <div class="action-row">
                <button class="btn-disc-ico" onclick="openDisc()" id="btn-disc" title="Add discount">
                    &#127991;
                    <span class="disc-dot" id="disc-dot"></span>
                </button>
                <button class="btn-charge" id="btn-chg" onclick="openPay()" disabled>
                    Charge &nbsp;£<span id="chg-amt">0.00</span>
                </button>
            </div>
        </div>

    </div>
</div>

</div>

{{-- PAYMENT MODAL --}}
<div class="modal fade" id="payModal" tabindex="-1" data-bs-backdrop="static">
<div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
<div class="modal-content border-0" style="border-radius:20px;box-shadow:0 24px 64px rgba(0,0,0,.15);background:var(--bs-body-bg);">
    <div class="modal-header border-0" style="padding:22px 22px 0;">
        <h6 class="modal-title" style="font-size:16px;font-weight:800;color:var(--bs-body-color);">Payment</h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" style="padding:16px 22px;">
        <div style="background:var(--bs-tertiary-bg);border-radius:14px;padding:18px;text-align:center;margin-bottom:16px;">
            <div style="font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--bs-secondary-color);margin-bottom:4px;">Total Due</div>
            <div style="font-size:44px;font-weight:800;font-family:'Syne',sans-serif;color:#10b981;letter-spacing:-2px;" id="pay-ttl">£0.00</div>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:16px;">
            <div class="pm-pill on" id="pm-cash" onclick="setPM('Cash',this)"><div class="pm-ico">&#128181;</div><div class="pm-lbl">Cash</div></div>
            <div class="pm-pill" id="pm-card" onclick="setPM('Card',this)"><div class="pm-ico">&#128179;</div><div class="pm-lbl">Card</div></div>
            <div class="pm-pill" id="pm-split" onclick="setPM('Split',this)"><div class="pm-ico">&#9986;</div><div class="pm-lbl">Split</div></div>
        </div>
        <div id="sec-cash">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--bs-secondary-color);margin-bottom:6px;">Amount Tendered</div>
            <input type="number" id="cash-in" class="form-control form-control-lg text-end fw-bold" style="font-size:28px;border-radius:12px;border:2px solid var(--bs-border-color);background:var(--bs-tertiary-bg);color:var(--bs-body-color);letter-spacing:-.5px;" step="0.01" min="0" placeholder="0.00" oninput="calcChg()">
            <div style="display:flex;gap:6px;margin-top:10px;">
                @foreach([5,10,20,50] as $a)
                <button onclick="setCash({{ $a }})" class="cash-tile">£{{ $a }}</button>
                @endforeach
                <button onclick="setExact()" class="cash-tile exact">Exact</button>
            </div>
            <div class="chg-box" id="chg-box" style="display:none;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#059669;margin-bottom:4px;">Change</div>
                <div style="font-size:28px;font-weight:800;font-family:'Syne',sans-serif;color:#10b981;letter-spacing:-1px;" id="chg-val">£0.00</div>
            </div>
        </div>
        <div id="sec-card" style="display:none;text-align:center;padding:24px 0;">
            <div style="font-size:52px;">&#128179;</div>
            <div style="font-size:13px;color:var(--bs-secondary-color);margin-top:8px;">Present card to terminal</div>
        </div>
        <div id="sec-split" style="display:none;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <div style="font-size:10px;color:var(--bs-secondary-color);margin-bottom:5px;">Cash £</div>
                    <input type="number" id="sp-cash" class="form-control" step="0.01" min="0" value="0" oninput="calcSplit()" style="border-radius:10px;background:var(--bs-tertiary-bg);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);font-size:16px;font-weight:700;">
                </div>
                <div>
                    <div style="font-size:10px;color:var(--bs-secondary-color);margin-bottom:5px;">Card £</div>
                    <input type="number" id="sp-card" class="form-control" step="0.01" min="0" value="0" oninput="calcSplit()" style="border-radius:10px;background:var(--bs-tertiary-bg);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);font-size:16px;font-weight:700;">
                </div>
            </div>
            <div id="sp-warn" style="color:#dc3545;font-size:11px;margin-top:6px;display:none;"></div>
        </div>
        <div style="margin-top:12px;">
            <div style="font-size:10px;color:var(--bs-secondary-color);margin-bottom:5px;">Notes (optional)</div>
            <input type="text" id="pay-notes" class="form-control form-control-sm" placeholder="e.g. customer ref..." style="border-radius:9px;background:var(--bs-tertiary-bg);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);">
        </div>
    </div>
    <div class="modal-footer border-0 pt-0" style="padding:0 22px 22px;">
        <button id="btn-done" onclick="completeSale()" style="width:100%;padding:14px;font-size:15px;font-weight:800;border:none;border-radius:12px;background:#10b981;color:#fff;cursor:pointer;box-shadow:0 4px 16px rgba(16,185,129,.3);letter-spacing:-.2px;transition:filter .15s;">
            Complete Sale
        </button>
    </div>
</div></div></div>

{{-- DISCOUNT MODAL --}}
<div class="modal fade" id="discModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
<div class="modal-content border-0" style="border-radius:18px;box-shadow:0 20px 56px rgba(0,0,0,.13);background:var(--bs-body-bg);">
    <div class="modal-header border-0" style="padding:18px 18px 0;">
        <h6 class="modal-title" style="font-size:15px;font-weight:800;color:var(--bs-body-color);">Discount</h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" style="padding:14px 18px;">
        <div style="display:flex;gap:8px;margin-bottom:14px;">
            <button id="d-pct" onclick="setDT('percent',this)" style="flex:1;padding:9px;border-radius:9px;border:2px solid var(--bs-primary);background:rgba(var(--bs-primary-rgb),.08);color:var(--bs-primary);font-weight:700;font-size:12px;cursor:pointer;transition:all .12s;">% Percent</button>
            <button id="d-fix" onclick="setDT('fixed',this)" style="flex:1;padding:9px;border-radius:9px;border:2px solid var(--bs-border-color);background:var(--bs-tertiary-bg);color:var(--bs-secondary-color);font-weight:700;font-size:12px;cursor:pointer;transition:all .12s;">£ Fixed</button>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <div id="d-pfx" style="width:40px;height:46px;display:flex;align-items:center;justify-content:center;background:rgba(var(--bs-primary-rgb),.1);border:2px solid rgba(var(--bs-primary-rgb),.2);border-radius:10px;font-weight:800;font-size:18px;color:var(--bs-primary);flex-shrink:0;">%</div>
            <input type="number" id="d-val" class="form-control" step="0.01" min="0" placeholder="0" style="height:46px;border-radius:10px;font-size:22px;font-weight:800;color:var(--bs-body-color);background:var(--bs-tertiary-bg);border:2px solid var(--bs-border-color);">
        </div>
    </div>
    <div class="modal-footer border-0" style="padding:0 18px 18px;flex-direction:column;gap:7px;">
        <button onclick="applyDisc()" style="width:100%;padding:11px;border-radius:10px;border:none;background:var(--bs-primary);color:#fff;font-weight:700;font-size:14px;cursor:pointer;box-shadow:0 3px 12px rgba(var(--bs-primary-rgb),.3);">Apply Discount</button>
        <button onclick="removeDisc()" style="width:100%;padding:9px;border-radius:10px;border:1.5px solid rgba(220,53,69,.25);background:rgba(220,53,69,.05);color:#dc3545;font-weight:600;font-size:12px;cursor:pointer;">Remove discount</button>
    </div>
</div></div></div>

{{-- CUSTOM ITEM --}}
<div class="modal fade" id="custModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
<div class="modal-content border-0" style="border-radius:18px;box-shadow:0 20px 56px rgba(0,0,0,.13);background:var(--bs-body-bg);">
    <div class="modal-header border-0" style="padding:18px 18px 0;">
        <h6 class="modal-title" style="font-size:15px;font-weight:800;color:var(--bs-body-color);">Custom item</h6>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" style="padding:14px 18px;">
        <div style="margin-bottom:12px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--bs-secondary-color);margin-bottom:5px;">Name *</div>
            <input type="text" id="cu-nm" class="form-control" placeholder="e.g. Screen fitting fee" style="border-radius:10px;background:var(--bs-tertiary-bg);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);">
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--bs-secondary-color);margin-bottom:5px;">Price £ *</div>
                <input type="number" id="cu-pr" class="form-control" step="0.01" min="0" placeholder="0.00" style="border-radius:10px;font-size:15px;font-weight:700;background:var(--bs-tertiary-bg);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);">
            </div>
            <div>
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--bs-secondary-color);margin-bottom:5px;">Qty</div>
                <input type="number" id="cu-qt" class="form-control" min="1" value="1" style="border-radius:10px;font-size:15px;font-weight:700;background:var(--bs-tertiary-bg);border:1.5px solid var(--bs-border-color);color:var(--bs-body-color);">
            </div>
        </div>
    </div>
    <div class="modal-footer border-0" style="padding:0 18px 18px;">
        <button onclick="addCustom()" style="width:100%;padding:12px;border-radius:10px;border:none;background:var(--bs-primary);color:#fff;font-weight:700;font-size:14px;cursor:pointer;box-shadow:0 3px 12px rgba(var(--bs-primary-rgb),.3);">Add to cart</button>
    </div>
</div></div></div>

{{-- SUCCESS --}}
<div class="modal fade" id="okModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered" style="max-width:320px;">
<div class="modal-content border-0" style="border-radius:20px;box-shadow:0 24px 64px rgba(0,0,0,.15);background:var(--bs-body-bg);">
    <div class="modal-body text-center" style="padding:36px 24px;">
        <div style="width:68px;height:68px;border-radius:50%;background:rgba(16,185,129,.1);border:2px solid rgba(16,185,129,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#10b981;font-weight:800;">&#10003;</div>
        <h5 style="font-weight:800;font-family:'Syne',sans-serif;color:var(--bs-body-color);margin-bottom:8px;font-size:20px;">Sale Complete!</h5>
        <div id="ok-summary" style="color:var(--bs-secondary-color);font-size:14px;margin-bottom:24px;line-height:1.6;"></div>
        <div style="display:flex;gap:8px;justify-content:center;">
            <a id="ok-receipt" href="#" target="_blank" style="padding:10px 18px;border-radius:10px;border:1.5px solid var(--bs-border-color);text-decoration:none;color:var(--bs-body-color);font-size:13px;font-weight:700;">Print receipt</a>
            <button onclick="newSale()" style="padding:10px 18px;border-radius:10px;border:none;background:#10b981;color:#fff;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(16,185,129,.25);">New sale</button>
        </div>
    </div>
</div></div></div>

@endsection

@push('scripts')
<script>
var cart=[], dType='percent', dVal=0, pMethod='Cash', drill='cat', curCat=null, curBrand=null, curModel=null, lastId=null;

// ── Load categories ───────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadCats);

function loadCats() {
    drill='cat'; curCat=null; curBrand=null; curModel=null; renderBC();
    setLoading();
    fetch('/pos/api/categories')
        .then(r=>r.json())
        .then(data => {
            if (!data.length) { setGrid('<div class="text-center text-secondary py-5" style="opacity:.5;"><div style="font-size:36px;">📭</div><div class="mt-2 small">No stock yet. <a href="{{ route("pos.stock") }}">Add stock →</a></div></div>'); return; }
            var h = '<div class="sh">Select a category</div><div class="pg">';
            data.forEach(c => {
                h += `<div class="pc" data-nav-cat="${c.id}" data-nav-name="${xe(c.name)}" data-nav-icon="${xe(c.icon)}" onclick="selectCat(this)">
                    <span class="pc-ico">${c.icon}</span>
                    <div class="pc-nm">${c.name}</div>
                </div>`;
            });
            h += `<div class="pc" onclick="loadServices()"><span class="pc-ico">🔧</div><div class="pc-nm">Services</div></div>`;
            setGrid(h+'</div>');
        }).catch(() => setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;">Failed to load. Refresh the page.</div>'));
}

function selectCat(el) {
    curCat = {id: el.dataset.navCat, name: el.dataset.navName, icon: el.dataset.navIcon};
    loadBrands(curCat.id, curCat.name, curCat.icon);
}

function loadBrands(catId, catName, catIcon) {
    drill='brand'; renderBC(); setLoading();
    fetch(`/pos/api/categories/${catId}/brands`)
        .then(r=>r.json())
        .then(data => {
            var brands = data.brands;
            if (!brands.length) { setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;"><div style="font-size:32px;">📭</div><div class="mt-2 small">No stock in this category.</div></div>'); return; }
            // If only 1 brand skip straight to models
            if (brands.length === 1 && brands[0].id !== 'universal') {
                curBrand = brands[0];
                loadModels(catId, brands[0].id, brands[0].name);
                return;
            }
            var h = `<div class="sh">${catIcon} ${catName} — Select Brand</div><div class="pg">`;
            brands.forEach(b => {
                h += `<div class="pc" data-nav-brand="${b.id}" data-nav-name="${xe(b.name)}" onclick="selectBrand(this)">
                    <span class="pc-ico">🏷️</span>
                    <div class="pc-nm">${b.name}</div>
                </div>`;
            });
            setGrid(h+'</div>');
        }).catch(e => setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;">Error loading brands.</div>'));
}

function selectBrand(el) {
    curBrand = {id: el.dataset.navBrand, name: el.dataset.navName};
    if (curBrand.id === 'universal') {
        loadProducts(curCat.id, 'universal', 'Universal');
    } else {
        loadModels(curCat.id, curBrand.id, curBrand.name);
    }
}

function loadModels(catId, brandId, brandName) {
    drill='model'; renderBC(); setLoading();
    fetch(`/pos/api/categories/${catId}/models?brand_id=${brandId}`)
        .then(r=>r.json())
        .then(data => {
            var models = data.models;
            if (!models.length) { setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;"><div style="font-size:32px;">📭</div><div class="mt-2 small">No models with stock.</div></div>'); return; }
            // If only 1 model skip straight to products
            if (models.length === 1) {
                curModel = models[0];
                loadProducts(catId, models[0].id, models[0].name);
                return;
            }
            var h = `<div class="sh">🏷️ ${brandName} — Select Model</div><div class="pg">`;
            models.forEach(m => {
                h += `<div class="pc" data-nav-model="${m.id}" data-nav-name="${xe(m.name)}" onclick="selectModel(this)">
                    <span class="pc-ico">📱</span>
                    <div class="pc-nm">${m.name}</div>
                </div>`;
            });
            setGrid(h+'</div>');
        }).catch(() => setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;">Error loading models.</div>'));
}

function selectModel(el) {
    curModel = {id: el.dataset.navModel, name: el.dataset.navName};
    loadProducts(curCat.id, curModel.id, curModel.name);
}

function loadProducts(catId, modelId, modelName) {
    drill='prod'; if (!curModel) curModel = {id:modelId, name:modelName}; renderBC(); setLoading();
    fetch(`/pos/api/categories/${catId}/products?model_id=${modelId}`)
        .then(r=>r.json())
        .then(data => {
            if (!data.length) { setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;"><div style="font-size:32px;">📭</div><div class="mt-2 small">No products in stock.</div></div>'); return; }
            var ico = curCat?.icon || '📦';
            var h = '<div class="pg">';
            data.forEach(p => {
                var oos = p.stock !== null && p.stock <= 0;
                var low = p.low_stock || (p.stock!==null && p.stock>0 && p.stock<=2);
                var st  = p.stock===null?'Service': oos?'Out of stock': low?`⚠️ ${p.stock} left`:`${p.stock} in stock`;
                h += `<div class="pc${oos?' oos':''}" ${!oos?`data-item-id="${p.id}" data-item-name="${xe(p.name)}" data-item-variant="${xe(p.variant||'')}" data-item-price="${p.price}" data-item-stock="${p.stock}" onclick="addFromCard(this)"`:''}>
                    <span class="pc-ico">${ico}</span>
                    <div class="pc-nm">${p.name}</div>
                    ${p.variant?`<div class="pc-var">${p.variant}</div>`:''}
                    <div class="pc-pr">£${parseFloat(p.price).toFixed(2)}</div>
                    <div class="pc-st${low?' low':''}">${st}</div>
                </div>`;
            });
            setGrid(h+'</div>');
        }).catch(()=>setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;">Error loading products.</div>'));
}

function loadServices() {
    curCat={id:'svc',name:'Services',icon:'🔧'}; curBrand={id:'svc',name:'All'}; curModel={id:'all',name:'All Services'};
    drill='prod'; renderBC(); setLoading();
    fetch('/pos/api/search?services=1')
        .then(r=>r.json())
        .then(data => {
            if (!data.length) { setGrid('<div class="text-center text-secondary py-4" style="opacity:.5;">No services found.</div>'); return; }
            var h = '<div class="pg">';
            data.forEach(p => {
                h += `<div class="pc" data-item-id="${xe(String(p.id))}" data-item-name="${xe(p.name)}" data-item-variant="" data-item-price="${p.price}" data-item-stock="null" onclick="addFromCard(this)">
                    <span class="pc-ico">🔧</span>
                    <div class="pc-nm">${p.name}</div>
                    <div class="pc-pr">£${parseFloat(p.price).toFixed(2)}</div>
                    <div class="pc-st">Service</div>
                </div>`;
            });
            setGrid(h+'</div>');
        });
}

// ── Back / Breadcrumb ─────────────────────────────────────────────
function goBack() {
    if (drill==='prod')  { curModel=null; if (curBrand && curCat.id!=='svc') loadModels(curCat.id, curBrand.id, curBrand.name); else loadBrands(curCat.id, curCat.name, curCat.icon); }
    else if (drill==='model') { curModel=null; loadBrands(curCat.id, curCat.name, curCat.icon); }
    else if (drill==='brand') { curBrand=null; loadCats(); }
    else loadCats();
}
function renderBC() {
    var bc=document.getElementById('breadcrumb'), bb=document.getElementById('back-btn');
    bb.style.display = drill==='cat'?'none':'';
    if (drill==='cat')   { bc.innerHTML='<span class="bc-cur">📦 Categories</span>'; return; }
    var base=`<span class="bc-link" onclick="loadCats()">📦</span><span class="bc-sep">›</span>`;
    if (drill==='brand') { bc.innerHTML=base+`<span class="bc-cur">${curCat.icon} ${curCat.name}</span>`; return; }
    var catLink=`<span class="bc-link" onclick="loadBrands('${curCat.id}','${xe(curCat.name)}','${xe(curCat.icon)}')">${curCat.icon} ${curCat.name}</span><span class="bc-sep">›</span>`;
    if (drill==='model') { bc.innerHTML=base+catLink+`<span class="bc-cur">🏷️ ${curBrand?.name||''}</span>`; return; }
    var brandLink = curBrand && curCat.id!=='svc' ? `<span class="bc-link" onclick="loadModels('${curCat.id}','${curBrand.id}','${xe(curBrand.name)}')">${curBrand.name}</span><span class="bc-sep">›</span>` : '';
    bc.innerHTML=base+catLink+brandLink+`<span class="bc-cur">📱 ${curModel?.name||''}</span>`;
}

// ── Cart ──────────────────────────────────────────────────────────
function addFromCard(el) {
    addItem({
        id:      el.dataset.itemId,
        name:    el.dataset.itemName,
        variant: el.dataset.itemVariant || '',
        price:   parseFloat(el.dataset.itemPrice),
        stock:   el.dataset.itemStock === 'null' ? null : parseInt(el.dataset.itemStock),
    }, el);
    el.classList.add('flash');
    setTimeout(()=>el.classList.remove('flash'), 600);
}
function addItem(item, el) {
    if (typeof item === 'string') item = JSON.parse(item);
    var ex = cart.find(i=>i.id===String(item.id));
    if (ex) {
        if (item.stock!==null && ex.qty>=item.stock) { toast('No more stock!'); return; }
        ex.qty++;
    } else {
        cart.push({id:String(item.id), name:item.name, variant:item.variant||'', price:parseFloat(item.price), qty:1, stock:item.stock===undefined?null:item.stock});
    }
    renderCart();
    if (el && el.classList) { el.classList.add('flash'); setTimeout(()=>el.classList.remove('flash'),600); }
}

function renderCart() {
    var body=document.getElementById('cart-body'), emp=document.getElementById('c-empty');
    body.querySelectorAll('.ci').forEach(e=>e.remove());
    if (!cart.length) {
        emp.style.display='flex';
        document.getElementById('btn-chg').disabled=true;
        document.getElementById('c-count').textContent=0;
        calcTotals(); return;
    }
    emp.style.display='none';
    var tq=0;
    cart.forEach((item,i)=>{
        tq+=item.qty;
        var d=document.createElement('div'); d.className='ci';
        d.innerHTML=`<div class="ci-inf"><div class="ci-nm" title="${xe(item.name)}">${item.name}</div>${item.variant?`<div class="ci-sb">${item.variant}</div>`:''}</div>
        <div class="qctrl"><button class="qb" onclick="qch(${i},-1)">−</button><span class="qn">${item.qty}</span><button class="qb" onclick="qch(${i},1)">+</button></div>
        <div class="ci-tot">£${(item.price*item.qty).toFixed(2)}</div>
        <button class="xb" onclick="rm(${i})">✕</button>`;
        body.appendChild(d);
    });
    document.getElementById('c-count').textContent=tq;
    document.getElementById('btn-chg').disabled=false;
    calcTotals();
}
function qch(i,d){ cart[i].qty+=d; if(cart[i].qty<=0)cart.splice(i,1); else if(cart[i].stock!==null&&cart[i].qty>cart[i].stock){cart[i].qty=cart[i].stock;toast('Max stock reached');} renderCart(); }
function rm(i){ cart.splice(i,1); renderCart(); }
function clearCart(){ cart=[]; dVal=0; renderCart(); updDiscBtn(); }
function calcTotals(){
    var sub=cart.reduce((a,i)=>a+i.price*i.qty,0);
    var disc=dType==='percent'?sub*dVal/100:Math.min(dVal,sub);
    var tot=Math.max(0,sub-disc);
    document.getElementById('t-sub').textContent='£'+sub.toFixed(2);
    document.getElementById('t-total').textContent='£'+tot.toFixed(2);
    document.getElementById('chg-amt').textContent=tot.toFixed(2);
    document.getElementById('pay-ttl').textContent='£'+tot.toFixed(2);
    var r=document.getElementById('t-disc-row');
    if(disc>0){r.style.display='flex';document.getElementById('t-disc-lbl').textContent=dType==='percent'?`Discount (${dVal}%)`:'Discount';document.getElementById('t-disc-val').textContent='-£'+disc.toFixed(2);}
    else r.style.display='none';
}

// ── Search ────────────────────────────────────────────────────────
var searchTimer;
document.getElementById('srch').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var q = this.value.trim();
    var box = document.getElementById('sr-drop');
    if (!q) { box.style.display='none'; return; }
    searchTimer = setTimeout(function() {
        fetch('/pos/api/search?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (!data.length) {
                    box.innerHTML = '<div style="padding:9px 12px;font-size:12px;color:var(--bs-secondary-color);">No results for "'+q+'"</div>';
                    box.style.display = '';
                    return;
                }
                box.innerHTML = data.map(p => `<div class="sr-row"
                    data-item-id="${xe(String(p.id))}"
                    data-item-name="${xe(p.display||p.name)}"
                    data-item-variant="${xe(p.variant||'')}"
                    data-item-price="${p.price}"
                    data-item-stock="${p.stock}"
                    onclick="addFromCard(this);document.getElementById('sr-drop').style.display='none';document.getElementById('srch').value='';">
                    <div class="sr-ic">${p.icon}</div>
                    <div><div class="sr-nm">${p.display||p.name}</div><div class="sr-meta">${p.cat}${p.model?' · '+p.model:''}</div></div>
                    <div class="sr-pr">£${parseFloat(p.price).toFixed(2)}</div>
                </div>`).join('');
                box.style.display = '';
            }).catch(() => { box.style.display = 'none'; });
    }, 250);
});
document.addEventListener('click', e => { if(!e.target.closest('.pos-search')) document.getElementById('sr-drop').style.display='none'; });

// ── Discount ──────────────────────────────────────────────────────
function openDisc(){bootstrap.Modal.getOrCreateInstance(document.getElementById('discModal')).show();}
function setDT(t,btn){
    dType=t;
    var pct=document.getElementById('d-pct'), fix=document.getElementById('d-fix');
    if(t==='percent'){
        pct.style.borderColor='#5b6af0';pct.style.background='#eef0ff';pct.style.color='#5b6af0';
        fix.style.borderColor='#e8eaf0';fix.style.background='#f5f6fa';fix.style.color='#9097b0';
    } else {
        fix.style.borderColor='#5b6af0';fix.style.background='#eef0ff';fix.style.color='#5b6af0';
        pct.style.borderColor='#e8eaf0';pct.style.background='#f5f6fa';pct.style.color='#9097b0';
    }
    document.getElementById('d-pfx').textContent=t==='percent'?'%':'£';
}
function applyDisc(){dVal=parseFloat(document.getElementById('d-val').value)||0;calcTotals();updDiscBtn();bootstrap.Modal.getInstance(document.getElementById('discModal')).hide();}
function removeDisc(){dVal=0;document.getElementById('d-val').value='';calcTotals();updDiscBtn();bootstrap.Modal.getInstance(document.getElementById('discModal')).hide();}
function updDiscBtn(){
    var b=document.getElementById('btn-disc');
    if(dVal>0){ b.classList.add('on'); }
    else { b.classList.remove('on'); }
}
function setDT(t,btn){
    dType=t;
    var pct=document.getElementById('d-pct'), fix=document.getElementById('d-fix');
    if(t==='percent'){
        pct.style.borderColor='var(--bs-primary)';pct.style.background='rgba(var(--bs-primary-rgb),.08)';pct.style.color='var(--bs-primary)';
        fix.style.borderColor='var(--bs-border-color)';fix.style.background='var(--bs-tertiary-bg)';fix.style.color='var(--bs-secondary-color)';
    } else {
        fix.style.borderColor='var(--bs-primary)';fix.style.background='rgba(var(--bs-primary-rgb),.08)';fix.style.color='var(--bs-primary)';
        pct.style.borderColor='var(--bs-border-color)';pct.style.background='var(--bs-tertiary-bg)';pct.style.color='var(--bs-secondary-color)';
    }
    document.getElementById('d-pfx').textContent=t==='percent'?'%':'£';
}
function setPM(m,el){pMethod=m;document.querySelectorAll('.pm-pill').forEach(p=>p.classList.remove('on'));el.classList.add('on');document.getElementById('sec-cash').style.display=m==='Cash'?'':'none';document.getElementById('sec-card').style.display=m==='Card'?'':'none';document.getElementById('sec-split').style.display=m==='Split'?'':'none';}

// ── Custom item ───────────────────────────────────────────────────
function openCustom(){bootstrap.Modal.getOrCreateInstance(document.getElementById('custModal')).show();setTimeout(()=>document.getElementById('cu-nm').focus(),400);}
function addCustom(){
    var nm=document.getElementById('cu-nm').value.trim(),pr=parseFloat(document.getElementById('cu-pr').value)||0,qt=parseInt(document.getElementById('cu-qt').value)||1;
    if(!nm)return;
    cart.push({id:'c_'+Date.now(),name:nm,variant:'',price:pr,qty:qt,stock:null});
    renderCart(); bootstrap.Modal.getInstance(document.getElementById('custModal')).hide();
}

// ── Payment ───────────────────────────────────────────────────────
function openPay(){if(!cart.length)return;calcTotals();document.getElementById('cash-in').value='';document.getElementById('chg-box').style.display='none';bootstrap.Modal.getOrCreateInstance(document.getElementById('payModal')).show();setTimeout(()=>{if(pMethod==='Cash')document.getElementById('cash-in').focus();},400);}
function setPM(m,el){pMethod=m;document.querySelectorAll('.pm-pill').forEach(p=>p.classList.remove('on'));el.classList.add('on');document.getElementById('sec-cash').style.display=m==='Cash'?'':'none';document.getElementById('sec-card').style.display=m==='Card'?'':'none';document.getElementById('sec-split').style.display=m==='Split'?'':'none';}
function setCash(a){document.getElementById('cash-in').value=a.toFixed(2);calcChg();}
function setExact(){var t=parseFloat(document.getElementById('pay-ttl').textContent.replace('£',''))||0;document.getElementById('cash-in').value=t.toFixed(2);calcChg();}
function calcChg(){var t=parseFloat(document.getElementById('pay-ttl').textContent.replace('£',''))||0,c=parseFloat(document.getElementById('cash-in').value)||0,b=document.getElementById('chg-box');if(c>0){b.style.display='';document.getElementById('chg-val').textContent='£'+Math.max(0,c-t).toFixed(2);}else b.style.display='none';}
function calcSplit(){var t=parseFloat(document.getElementById('pay-ttl').textContent.replace('£',''))||0,c=parseFloat(document.getElementById('sp-cash').value)||0,k=parseFloat(document.getElementById('sp-card').value)||0,w=document.getElementById('sp-warn');if(Math.abs(c+k-t)>0.01){w.style.display='';w.textContent=`Total £${t.toFixed(2)}, entered £${(c+k).toFixed(2)}`;}else w.style.display='none';}

async function completeSale(){
    var btn=document.getElementById('btn-done'); btn.disabled=true; btn.textContent='Processing...';
    var sub=cart.reduce((a,i)=>a+i.price*i.qty,0);
    var disc=dType==='percent'?sub*dVal/100:Math.min(dVal,sub);
    var tot=Math.max(0,sub-disc);
    var paid=pMethod==='Cash'?(parseFloat(document.getElementById('cash-in').value)||0):pMethod==='Card'?tot:(parseFloat(document.getElementById('sp-cash').value)||0)+(parseFloat(document.getElementById('sp-card').value)||0);
    var pNotes=pMethod==='Split'?JSON.stringify([{type:'Cash',amount:parseFloat(document.getElementById('sp-cash').value)||0},{type:'Card',amount:parseFloat(document.getElementById('sp-card').value)||0}]):(document.getElementById('pay-notes').value||null);
    var items=cart.map(i=>({id:i.id,name:i.name+(i.variant?' ('+i.variant+')':''),qty:i.qty,price:i.price}));
    try {
        var res=await fetch('/pos',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},body:JSON.stringify({items,payment_method:pMethod,paid,payment_notes:pNotes,discount_type:dVal>0?dType:null,discount_value:dVal,customer_id:document.getElementById('cust-sel').value||null})});
        var data=await res.json();
        if(data.success){
            lastId=data.sale_id;
            bootstrap.Modal.getInstance(document.getElementById('payModal')).hide();
            var chg=Math.max(0,paid-tot);
            document.getElementById('ok-summary').innerHTML=`<strong>£${tot.toFixed(2)}</strong> via ${pMethod}`+(chg>0?`<br>Change: <strong>£${chg.toFixed(2)}</strong>`:'');
            document.getElementById('ok-receipt').href='/pos/'+lastId+'/receipt';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('okModal')).show();
        } else alert('Error: '+(data.message||'Unknown'));
    } catch(e){alert('Network error.');}
    btn.disabled=false; btn.innerHTML='✅ Complete Sale';
}

function newSale(){clearCart();bootstrap.Modal.getInstance(document.getElementById('okModal'))?.hide();document.getElementById('cust-sel').value='';loadCats();}
function setGrid(h){document.getElementById('grid-area').innerHTML=h;}
function setLoading(){setGrid('<div class="d-flex align-items-center justify-content-center" style="height:180px;opacity:.3;"><div class="text-center"><div style="font-size:28px;">⏳</div><div class="small mt-1">Loading...</div></div></div>');}
function xe(s){return String(s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;');}
function toast(msg){var t=document.createElement('div');t.style.cssText='position:fixed;bottom:24px;right:24px;z-index:9999;padding:9px 16px;border-radius:10px;font-size:12px;font-weight:600;color:#fff;background:#e67e22;box-shadow:0 4px 12px rgba(0,0,0,.2);';t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.remove(),2500);}

</script>
@endpush