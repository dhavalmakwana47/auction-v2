<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Challenge Mechanism Report – {{ $auction->corporate_debtor_name }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 30px 40px; line-height: 1.5; }
    h1 { font-size: 15px; text-align: center; margin-bottom: 4px; }
    .subtitle { font-size: 12px; text-align: center; margin-bottom: 20px; color: #444; }
    .cd-name { font-size: 13px; font-weight: bold; margin-bottom: 16px; }
    .section-title { font-size: 13px; font-weight: bold; margin: 20px 0 8px; border-bottom: 1px solid #999; padding-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 11.5px; }
    th, td { border: 1px solid #aaa; padding: 6px 8px; vertical-align: top; }
    thead th { background: #d0e8f8; font-weight: bold; text-align: center; }
    tbody tr:nth-child(even) { background: #f9f9f9; }
    tfoot td { background: #e8f5e9; font-weight: bold; }
    .no-bid { color: #c0392b; font-weight: bold; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bullet { margin-left: 16px; margin-bottom: 4px; }
    .footer { margin-top: 40px; font-size: 11px; color: #666; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; }
    @media print {
        body { padding: 10px; }
        .no-print { display: none; }
    }
</style>
</head>
<body>

<div class="no-print" style="text-align:right; margin-bottom:16px;">
    <button onclick="window.print()" style="padding:8px 20px; background:#059669; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer;">
        🖨 Print / Save as PDF
    </button>
</div>

<h1>REPORT on the Proceedings of the Challenge Mechanism</h1>
<div class="subtitle">
    during the CoC meeting held on
    {{ \Carbon\Carbon::parse($auction->meeting_date)->format('d.m.Y') }}
    THROUGH ELECTRONIC PLATFORM
</div>

<div class="cd-name">Corporate Debtor Name: {{ $auction->corporate_debtor_name }} – In CIRP</div>

{{-- ── Section A ── --}}
<div class="section-title">A. Timeline of Challenge Process</div>
<div class="bullet">• Start of Challenge Process: {{ $auction->started_at ? $auction->started_at->format('d.m.Y') . ' at ' . $auction->started_at->format('h:i A') : '—' }}</div>
<div class="bullet">• End of the Challenge Process: {{ $auction->ended_at ? $auction->ended_at->format('d.m.Y') . ' at ' . $auction->ended_at->format('h:i A') : '—' }}</div>

{{-- ── Section B ── --}}
<div class="section-title">B. Summary of the Challenge Process</div>
<p style="font-size:11px; margin-bottom:8px; color:#555;">(Maximum Resolution Amount submitted by the respective Resolution Applicants)</p>

<table>
    <thead>
        <tr>
            <th style="width:40px;">Sl. No.</th>
            <th>Name of Resolution Applicant</th>
            <th>Bid Sl. No.</th>
            <th>Resolution Amount (&#8377;)</th>
            <th>NPV of R/Amount (&#8377;)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bestBids as $i => $row)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $row['user']->name ?? '&mdash;' }}</td>
            <td class="text-center">{{ $row['best'] ? $bidIndexMap[$row['best']->id] : '&mdash;' }}</td>
            <td class="text-right">
                @if($row['best'])
                    {{ number_format($row['best']->bid_amount, 2) }}
                @else
                    <span class="no-bid">NO BID</span>
                @endif
            </td>
            <td class="text-right">{{ $row['best'] ? number_format($row['best']->total_npv, 2) : '&mdash;' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ── Section C ── --}}
<div class="section-title">C. Details of the Bids submitted by RAs during the Challenge Process</div>

<table>
    <thead>
        <tr>
            <th style="width:40px;">Bid Sl. No.</th>
            <th>Base Bid Value (&#8377;)</th>
            <th>Name of Resolution Applicant</th>
            <th>Date / Time Stamp</th>
            <th>Resolution Amount (&#8377;)</th>
            <th>NPV of R/Amount (&#8377;)</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allBids as $row)
        @php $bid = $row['bid']; @endphp
        <tr>
            <td class="text-center">{{ $bidIndexMap[$bid->id] }}</td>
            <td class="text-right">{{ number_format($row['base'], 2) }}</td>
            <td>{{ $bid->user->name ?? '&mdash;' }}</td>
            <td class="text-center" style="white-space:nowrap;">{{ $bid->created_at->format('d.m.Y') }} &ndash; {{ $bid->created_at->format('H:i') }}</td>
            <td class="text-right">{{ number_format($bid->bid_amount, 2) }}</td>
            <td class="text-right">{{ number_format($bid->total_npv, 2) }}</td>
            <td>{{ $row['remark'] }}</td>
        </tr>
        @endforeach
        @if(empty($allBids))
        <tr><td colspan="7" class="text-center" style="color:#999; padding:12px;">No bids placed.</td></tr>
        @endif
    </tbody>
</table>

<div class="footer">
    Generated by India E-Voting Platform &nbsp;|&nbsp; {{ now()->format('d.m.Y H:i') }}
</div>

</body>
</html>
