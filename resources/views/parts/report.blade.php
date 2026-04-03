<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Report — {{ \App\Models\Setting::get('shop_name','Mobile Shop') }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; }

        .print-controls { background: #1C1C1E; color: #fff; padding: 12px 20px; display: flex; gap: 10px; align-items: center; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; }
        .btn-print { background: #0A84FF; color: #fff; }
        .btn-back  { background: #3a3a3a; color: #fff; }

        .report-wrap { padding: 24px 28px; max-width: 1100px; margin: 0 auto; }

        /* Header */
        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #1C1C1E; }
        .report-header .shop { font-size: 20px; font-weight: 800; }
        .report-header .sub  { font-size: 12px; color: #666; margin-top: 3px; }
        .report-header .date { text-align: right; font-size: 12px; color: #666; }

        /* Summary cards */
        .summary { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 20px; }
        .sum-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 12px 14px; }
        .sum-card .label { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
        .sum-card .value { font-size: 20px; font-weight: 800; }
        .sum-card.blue  { border-top: 3px solid #0A84FF; } .sum-card.blue  .value { color: #0A84FF; }
        .sum-card.green { border-top: 3px solid #30D158; } .sum-card.green .value { color: #197a33; }
        .sum-card.orange{ border-top: 3px solid #FF9F0A; } .sum-card.orange .value { color: #b36500; }
        .sum-card.yellow{ border-top: 3px solid #FFD60A; } .sum-card.yellow .value { color: #856404; }
        .sum-card.red   { border-top: 3px solid #FF453A; } .sum-card.red   .value { color: #c00; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 4px; }
        thead th { background: #1C1C1E; color: #fff; padding: 8px 10px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #eee; }
        tbody tr:hover { background: #f9f9f9; }
        tbody tr.out-of-stock { background: #fff5f5; }
        tbody tr.low-stock    { background: #fffbf0; }
        tbody td { padding: 8px 10px; vertical-align: middle; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; }
        .badge-ok   { background: #d4f7dc; color: #1a7a33; }
        .badge-warn { background: #fff3cd; color: #856404; }
        .bg-danger{ background: #fde2e2; color: #a01010; }

        /* Category group header */
        .cat-header td { background: #f0f0f0; font-weight: 700; font-size: 11px; color: #444; padding: 6px 10px; letter-spacing: .04em; }

        /* Footer */
        .report-footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 11px; color: #888; display: flex; justify-content: space-between; }

        @media print {
            .print-controls { display: none; }
            .report-wrap { padding: 12px; }
            body { font-size: 11px; }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <button class="btn btn-print" onclick="window.print()">🖨️ Print / Save PDF</button>
    <a class="btn btn-back" href="{{ route('parts.index') }}">← Back to Stock</a>
    <span style="margin-left:auto;font-size:12px;color:#8E8E93;">{{ $parts->count() }} parts · Generated {{ now()->format('d M Y, H:i') }}</span>
</div>

<div class="report-wrap">

    {{-- Header --}}
    <div class="report-header">
        <div>
            <div class="shop">📱 {{ \App\Models\Setting::get('shop_name','Mobile Shop') }}</div>
            <div class="sub">Repair Stock Report</div>
            @if(\App\Models\Setting::get('shop_address'))
            <div class="sub">{{ \App\Models\Setting::get('shop_address') }}</div>
            @endif
        </div>
        <div class="date">
            <div style="font-size:16px;font-weight:700;">Stock Report</div>
            <div>{{ now()->format('d M Y') }}</div>
            <div>{{ now()->format('H:i') }}</div>
        </div>
    </div>

    {{-- Summary --}}
    @php
        $totalItems = $parts->count();
        $totalUnits = $parts->sum('stock');
        $outOfStock = $parts->filter(fn($p) => $p->stock <= 0)->count();
        $lowStock   = $parts->filter(fn($p) => $p->stock > 0 && $p->stock <= 2)->count();
        $inStock    = $parts->filter(fn($p) => $p->stock > 2)->count();
    @endphp
    <div class="summary">
        <div class="sum-card blue">
            <div class="label">Total Parts</div>
            <div class="value">{{ $totalItems }}</div>
        </div>
        <div class="sum-card green">
            <div class="label">Total Units</div>
            <div class="value">{{ $totalUnits }}</div>
        </div>
        <div class="sum-card orange">
            <div class="label">Cost Value</div>
            <div class="value">£{{ number_format($totalStockValue, 0) }}</div>
        </div>
        <div class="sum-card yellow">
            <div class="label">Low Stock</div>
            <div class="value">{{ $lowStock }}</div>
        </div>
        <div class="sum-card red">
            <div class="label">Out of Stock</div>
            <div class="value">{{ $outOfStock }}</div>
        </div>
    </div>

    {{-- Parts table grouped by category --}}
    @php $grouped = $parts->groupBy('category.name'); @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Part Name</th>
                <th>Type</th>
                <th>Quality</th>
                <th style="text-align:center;">Stock</th>
                <th style="text-align:center;">Used</th>
                <th style="text-align:center;">Remaining</th>
                <th style="text-align:right;">Cost £</th>
                <th style="text-align:right;">Sell £</th>
                <th style="text-align:right;">Stock Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @php $rowNum = 1; @endphp
        @foreach($grouped as $catName => $catParts)
        <tr class="cat-header">
            <td colspan="11">
                {{ $catParts->first()->category->icon ?? '📦' }} {{ $catName }} — {{ $catParts->count() }} part(s), {{ $catParts->sum('stock') }} units
            </td>
        </tr>
        @foreach($catParts as $part)
        @php
            $remaining  = $part->remainingStock();
            $rowClass   = $remaining <= 0 ? 'out-of-stock' : ($remaining <= 2 ? 'low-stock' : '');
            $badgeClass = $remaining <= 0 ? 'bg-danger' : ($remaining <= 2 ? 'badge-warn' : 'badge-ok');
            $badgeLabel = $remaining <= 0 ? 'Out of Stock' : ($remaining <= 2 ? 'Low' : 'OK');
        @endphp
        <tr class="{{ $rowClass }}">
            <td style="color:#888;">{{ $rowNum++ }}</td>
            <td style="font-weight:600;">{{ $part->name }}</td>
            <td>{{ $partTypes[$part->part_type] ?? '🔩' }} {{ $part->part_type }}</td>
            <td>{{ $part->quality }}</td>
            <td style="text-align:center;font-weight:700;">{{ $part->stock }}</td>
            <td style="text-align:center;color:#666;">{{ $part->usedCount() }}</td>
            <td style="text-align:center;font-weight:700;{{ $remaining <= 0 ? 'color:#c00;' : ($remaining <= 2 ? 'color:#856404;' : 'color:#1a7a33;') }}">{{ $remaining }}</td>
            <td style="text-align:right;color:#666;">{{ $part->cost_price ? '£'.number_format($part->cost_price,2) : '—' }}</td>
            <td style="text-align:right;font-weight:600;">{{ $part->sell_price ? '£'.number_format($part->sell_price,2) : '—' }}</td>
            <td style="text-align:right;color:#666;">{{ $part->cost_price ? '£'.number_format($part->cost_price * $part->stock,2) : '—' }}</td>
            <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
        </tr>
        @endforeach
        @endforeach
        {{-- Totals row --}}
        <tr style="background:#1C1C1E;color:#fff;font-weight:700;">
            <td colspan="4" style="padding:10px;">TOTAL</td>
            <td style="text-align:center;padding:10px;">{{ $parts->sum('stock') }}</td>
            <td style="text-align:center;padding:10px;color:#8E8E93;">—</td>
            <td style="text-align:center;padding:10px;">{{ $parts->sum(fn($p)=>$p->remainingStock()) }}</td>
            <td style="padding:10px;text-align:right;color:#8E8E93;">—</td>
            <td style="padding:10px;text-align:right;color:#8E8E93;">—</td>
            <td style="padding:10px;text-align:right;">£{{ number_format($totalStockValue, 2) }}</td>
            <td style="padding:10px;"></td>
        </tr>
        </tbody>
    </table>

    <div class="report-footer">
        <span>{{ \App\Models\Setting::get('shop_name','Mobile Shop') }} · Stock Report</span>
        <span>Printed {{ now()->format('d M Y H:i') }}</span>
    </div>

</div>
</body>
</html>
