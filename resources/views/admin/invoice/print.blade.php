<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Nota - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .ticket {
            width: 80mm; /* Standar kertas kasir 80mm */
            max-width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            box-sizing: border-box;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .font-bold {
            font-weight: bold;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        .w-full {
            width: 100%;
        }
        .item-name {
            display: block;
            margin-bottom: 2px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .ticket {
                width: 100%; /* Mengisi lebar media printer */
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="no-print" style="text-align: center; margin-bottom: 20px; padding: 10px; background: #f8f9fa;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #000; color: #fff; border: none; border-radius: 5px;">Cetak Nota Ulang</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #ccc; color: #333; border: none; border-radius: 5px; margin-left: 10px;">Tutup</button>
    </div>

    <div class="ticket">
        <div class="text-center">
            <div class="title">BengkelPro</div>
            <div class="subtitle">Jl. Raya Bengkel No. 123<br>Telp: 0812-3456-7890</div>
        </div>

        <div class="divider"></div>

        <table>
            <tr>
                <td style="width: 40%;">No. Nota</td>
                <td style="width: 5%;">:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ date('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>:</td>
                <td>{{ auth()->user()->name ?? 'Kasir' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>:</td>
                <td>{{ $invoice->serviceOrder->customer->name ?? 'Umum' }}</td>
            </tr>
            @if($invoice->serviceOrder->vehicle)
            <tr>
                <td>Kendaraan</td>
                <td>:</td>
                <td>{{ $invoice->serviceOrder->vehicle->license_plate }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>

        <table>
            @foreach($invoice->serviceOrder->items as $item)
                <tr>
                    <td colspan="3"><span class="item-name">{{ $item->item_name }}</span></td>
                </tr>
                <tr>
                    <td style="width: 25%;">{{ $item->quantity }} x</td>
                    <td style="width: 35%;" class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td style="width: 40%;" class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <table>
            <tr>
                <td style="width: 50%;">Subtotal</td>
                <td style="width: 10%;">:</td>
                <td class="text-right">{{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            @php
                $taxPercent = $invoice->subtotal > 0 ? round(($invoice->tax / $invoice->subtotal) * 100) : 0;
                $discPercent = $invoice->subtotal > 0 ? round(($invoice->discount / $invoice->subtotal) * 100) : 0;
            @endphp
            @if($invoice->tax > 0)
            <tr>
                <td>Pajak ({{ $taxPercent }}%)</td>
                <td>:</td>
                <td class="text-right">{{ number_format($invoice->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td>Diskon ({{ $discPercent }}%)</td>
                <td>:</td>
                <td class="text-right">-{{ number_format($invoice->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td class="font-bold" style="font-size: 14px;">TOTAL</td>
                <td class="font-bold" style="font-size: 14px;">:</td>
                <td class="text-right font-bold" style="font-size: 14px;">{{ number_format($invoice->grand_total, 0, ',', '.') }}</td>
            </tr>
        </table>
        
        <div class="divider"></div>
        
        <table>
            <tr>
                <td style="width: 50%;">Status Bayar</td>
                <td style="width: 10%;">:</td>
                <td class="text-right font-bold">{{ $invoice->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}</td>
            </tr>
            @if($invoice->payment_status === 'paid')
            <tr>
                <td>Metode</td>
                <td>:</td>
                <td class="text-right" style="text-transform: uppercase;">{{ $invoice->payment_method }}</td>
            </tr>
            @endif
        </table>

        <div class="divider"></div>
        
        <div class="text-center" style="margin-top: 10px;">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli<br>tidak dapat dikembalikan.</p>
        </div>
    </div>
</body>
</html>
