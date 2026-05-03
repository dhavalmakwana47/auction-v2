<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #222; line-height: 1.5; margin: 20px; }
    .notice { font-size: 10px; color: #888; margin-bottom: 14px; }
    .section { font-weight: bold; margin: 14px 0 6px; font-size: 12px; text-decoration: underline; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 11px; }
    th, td { border: 1px solid #bbb; padding: 5px 7px; }
    thead tr { background: #d0e8f8; }
    tfoot tr { background: #f0f0f0; font-weight: bold; }
    .label-col { background: #f5f5f5; font-weight: bold; width: 45%; }
    .footer { margin-top: 30px; font-size: 11px; }
</style>
</head>
<body>

<p class="notice">(Please do not reply to this email, as the sender's email address is not monitored)</p>

<p>Dear Sir,</p>
<p>In CIRP Matters of <strong>{{ $bid->auction->corporate_debtor_name }} – In CIRP</strong><br>
Please note as follows –</p>

<div class="section">A. Bid Submission Details</div>
<table>
    <tr><td class="label-col">Bid Sl. No.</td><td>{{ $bid->id }}</td></tr>
    <tr><td class="label-col">Date / Time of Bid Submission</td><td>{{ $bid->created_at->format('d.m.Y') }} at {{ $bid->created_at->format('h:i A') }}</td></tr>
    <tr><td class="label-col">RA's Name</td><td>{{ $bid->user->name }}</td></tr>
    <tr><td class="label-col">RA's IP Address</td><td>{{ $bid->ip_address ?? '—' }}</td></tr>
</table>

@php
    $configs    = $bid->auction->npvpConfigurations;
    $categories = $bid->auction->npvCategories;
    $distMap    = [];
    foreach ($bid->distributions as $d) {
        $distMap[$d->npv_category_id][$d->npvp_configuration_id] = $d->amount;
    }
    $colTotals  = [];
    foreach ($configs as $cfg) { $colTotals[$cfg->id] = 0; }
    $grandTotal = 0;
@endphp

<div class="section">
    B. Bid Submitted during Challenge Mechanism during CoC meeting held on
    {{ \Carbon\Carbon::parse($bid->auction->meeting_date)->format('d.m.Y') }}
    THROUGH Indian E-voting Platform (Service Provider)
</div>

<table>
    <thead>
        <tr>
            <th align="left">Category</th>
            @foreach($configs as $cfg)<th align="center">{{ $cfg->period }} Days</th>@endforeach
            <th align="right">Total (&#8377;)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $cat)
        @php $rowTotal = 0; @endphp
        <tr>
            <td>{{ $cat->name }}</td>
            @foreach($configs as $cfg)
                @php
                    $amt = $distMap[$cat->id][$cfg->id] ?? 0;
                    $rowTotal += $amt;
                    $colTotals[$cfg->id] += $amt;
                    $grandTotal += $amt;
                @endphp
                <td align="right">{{ $amt > 0 ? number_format($amt, 2) : '—' }}</td>
            @endforeach
            <td align="right"><strong>{{ number_format($rowTotal, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            @foreach($configs as $cfg)
                <td align="right">{{ number_format($colTotals[$cfg->id], 2) }}</td>
            @endforeach
            <td align="right">{{ number_format($grandTotal, 2) }}</td>
        </tr>
    </tfoot>
</table>

<p><strong>Remarks:</strong> Bid Amount: &#8377; {{ number_format($bid->bid_amount, 2) }} | NPV: &#8377; {{ number_format($bid->total_npv, 2) }}</p>

<div class="footer">
    Thanks &amp; regards,<br>
    <strong>India E-Voting Support Service</strong>
</div>

</body>
</html>
