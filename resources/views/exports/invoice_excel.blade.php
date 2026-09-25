<html>
<head><meta charset="utf-8"></head>
<body>
    <table border="1" style="border-collapse: collapse; font-family: sans-serif;">
        <tr>
            <th colspan="7" style="background-color: #1e293b; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; height: 40px;">
                LAPORAN PENDAPATAN BENGKELPRO - {{ $title }}
            </th>
        </tr>
        <tr style="background-color: #FF6B00; color: #ffffff; text-align: center; font-weight: bold;">
            <th style="width: 120px;">Tanggal</th>
            <th style="width: 150px;">No. Invoice</th>
            <th style="width: 200px;">Pelanggan</th>
            <th style="width: 150px;">Plat Nomor</th>
            <th style="width: 120px;">Metode Bayar</th>
            <th style="width: 120px;">Status</th>
            <th style="width: 150px;">Total (Rp)</th>
        </tr>
        
        @php
            $totalPendapatan = 0;
            $totalQris = 0;
            $totalCash = 0;
        @endphp

        @foreach($invoices as $inv)
            @php
                if ($inv->payment_status === 'paid') {
                    $totalPendapatan += $inv->grand_total;
                    if (strtolower($inv->payment_method) === 'qris') {
                        $totalQris += $inv->grand_total;
                    } elseif (strtolower($inv->payment_method) === 'cash') {
                        $totalCash += $inv->grand_total;
                    }
                }
                $statusColor = $inv->payment_status === 'paid' ? '#16a34a' : '#dc2626';
            @endphp
            
            <tr style="text-align: center;">
                <td>{{ $inv->created_at->format('Y-m-d') }}</td>
                <td>{{ $inv->invoice_number }}</td>
                <td style="text-align: left;">{{ $inv->serviceOrder->vehicle->customer->name ?? '-' }}</td>
                <td>{{ $inv->serviceOrder->vehicle->license_plate ?? '-' }}</td>
                <td>{{ strtoupper($inv->payment_method) }}</td>
                <td style="color: {{ $statusColor }}; font-weight: bold;">{{ strtoupper($inv->payment_status) }}</td>
                <td style="text-align: right;">{{ number_format($inv->grand_total, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        
        <tr>
            <th colspan="6" style="text-align: right; font-weight: bold; font-size: 14px; background-color: #f8fafc; height: 30px;">TOTAL PEMBAYARAN QRIS</th>
            <th style="text-align: right; font-weight: bold; font-size: 14px; background-color: #f8fafc; color: #0284c7;">Rp {{ number_format($totalQris, 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: right; font-weight: bold; font-size: 14px; background-color: #f8fafc; height: 30px;">TOTAL PEMBAYARAN TUNAI (CASH)</th>
            <th style="text-align: right; font-weight: bold; font-size: 14px; background-color: #f8fafc; color: #ea580c;">Rp {{ number_format($totalCash, 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: right; font-weight: bold; font-size: 15px; background-color: #e2e8f0; height: 35px;">TOTAL KESELURUHAN (LUNAS)</th>
            <th style="text-align: right; font-weight: bold; font-size: 15px; background-color: #e2e8f0; color: #16a34a;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
        </tr>
    </table>
</body>
</html>
