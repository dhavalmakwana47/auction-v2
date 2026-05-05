<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.6;">

<p style="color:#888; font-size:12px;">(Please do not reply to this email, as the sender's email address is not monitored)</p>

<p>Dear Sir,</p>

<p>In CIRP Matters of<br>
<strong>{{ $bid->auction->corporate_debtor_name }} – In CIRP</strong></p>

<p>Please note as follows –</p>

<p><strong>A. Bid Submission Details</strong></p>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%; font-size:13px;">
    <tr style="background:#f0f0f0;">
        <th align="left" width="40%">Bid Sl. No.</th>
        <td>{{ $bidIndex }}</td>
    </tr>
    <tr>
        <th align="left">Date / Time of Bid Submission</th>
        <td>{{ $bid->created_at->format('d.m.Y') }} at {{ $bid->created_at->format('h:i A') }}</td>
    </tr>
    <tr style="background:#f0f0f0;">
        <th align="left">RA's Name</th>
        <td>{{ $bid->user->name }}</td>
    </tr>
    <tr>
        <th align="left">RA's IP Address</th>
        <td>{{ $bid->ip_address ?? '—' }}</td>
    </tr>
</table>

<br>

<p><strong>B. Bid Submitted during Challenge Mechanism during CoC meeting held on
{{ \Carbon\Carbon::parse($bid->auction->meeting_date)->format('d.m.Y') }}
THROUGH Indian E-voting Platform (Service Provider)</strong></p>

@php
    $configs    = $bid->auction->npvpConfigurations;
    $categories = $bid->auction->npvCategories;
    $distMap    = [];
    foreach ($bid->distributions as $d) {
        $distMap[$d->npv_category_id][$d->npvp_configuration_id] = $d->amount;
    }
    $colTotals    = [];
    $colNpvTotals = [];
    foreach ($configs as $cfg) {
        $colTotals[$cfg->id]    = 0;
        $colNpvTotals[$cfg->id] = 0;
    }
    $grandTotal    = 0;
    $grandNpvTotal = 0;
@endphp

<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%; font-size:12px;">
    <thead>
        <tr style="background:#d0e8f8; text-align:center;">
            <th align="left">Category</th>
            @foreach($configs as $cfg)
                <th>{{ $cfg->period }} Days</th>
            @endforeach
            <th>Total (&#8377;)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $cat)
        @php $rowTotal = 0; $rowNpv = 0; @endphp
        <tr>
            <td>{{ $cat->name }}</td>
            @foreach($configs as $cfg)
                @php
                    $amt    = $distMap[$cat->id][$cfg->id] ?? 0;
                    $npvVal = $amt * (float) $cfg->percentage_value;
                    $rowTotal += $amt;
                    $rowNpv   += $npvVal;
                    $colTotals[$cfg->id]    += $amt;
                    $colNpvTotals[$cfg->id] += $npvVal;
                    $grandTotal    += $amt;
                    $grandNpvTotal += $npvVal;
                @endphp
                <td align="right">{{ $amt > 0 ? number_format($amt, 2) : '—' }}</td>
            @endforeach
            <td align="right"><strong>{{ number_format($rowTotal, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background:#f0f0f0; font-weight:bold;">
            <td>Total</td>
            @foreach($configs as $cfg)
                <td align="right">{{ number_format($colTotals[$cfg->id], 2) }}</td>
            @endforeach
            <td align="right">{{ number_format($grandTotal, 2) }}</td>
        </tr>
        <tr style="background:#e0f7f5; font-weight:bold;">
            <td>NPV Total</td>
            @foreach($configs as $cfg)
                <td align="right">{{ number_format($colNpvTotals[$cfg->id], 2) }}</td>
            @endforeach
            <td align="right">{{ number_format($grandNpvTotal, 2) }}</td>
        </tr>
    </tfoot>
</table>

<br>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse; width:100%; font-size:13px;">
    <tr style="background:#f0f0f0;">
        <th align="left" width="40%">Bid Amount (&#8377;)</th>
        <td>{{ number_format($bid->bid_amount, 2) }}</td>
    </tr>
    <tr>
        <th align="left">NPV of Bid Amount (&#8377;)</th>
        <td>{{ number_format($bid->total_npv, 2) }}</td>
    </tr>
    <tr style="background:#f0f0f0;">
        <th align="left">Remarks</th>
        <td>{{ $remark }}</td>
    </tr>
</table>

<br>
<p>Thanks &amp; regards,<br>
<strong>India E-Voting Support Service</strong></p>

</body>
</html>
