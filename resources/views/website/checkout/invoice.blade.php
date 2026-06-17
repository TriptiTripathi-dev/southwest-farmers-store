<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .header-table td {
            vertical-align: top;
        }
        .brand-logo {
            font-size: 28px;
            font-weight: bold;
            color: #009A36;
            margin: 0;
            line-height: 1;
        }
        .brand-subtitle {
            font-size: 12px;
            color: #666;
            margin: 2px 0 0 0;
        }
        .invoice-title {
            text-align: right;
            font-size: 32px;
            font-weight: 300;
            color: #333;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .meta-table td {
            width: 50%;
            vertical-align: top;
            font-size: 13px;
        }
        .meta-label {
            font-weight: bold;
            color: #666;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 4px;
            display: block;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            width: 50%;
            vertical-align: top;
            font-size: 13px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
            padding: 12px;
            font-weight: bold;
            font-size: 12px;
            color: #475569;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #334155;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .summary-table .label {
            text-align: right;
            color: #64748b;
            font-weight: bold;
            width: 80%;
        }
        .summary-table .value {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }
        .summary-table .grand-total td {
            padding-top: 12px;
            border-top: 2px solid #e2e8f0;
        }
        .summary-table .grand-total .label {
            font-size: 15px;
            color: #0f172a;
        }
        .summary-table .grand-total .value {
            font-size: 20px;
            color: #009A36;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .badge-failed {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .badge-pending {
            background-color: #fffbeb;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Brand & Title Header -->
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="brand-logo">SOUTHWEST FARMERS</h1>
                    <p class="brand-subtitle">Fresh Produce & Farming Essentials</p>
                </td>
                <td>
                    <h2 class="invoice-title">Invoice</h2>
                </td>
            </tr>
        </table>

        <!-- Metadata Section -->
        <table class="meta-table">
            <tr>
                <td>
                    <span class="meta-label">Invoice To</span>
                    <strong>{{ $order->customer->name ?? 'Customer Name' }}</strong><br>
                    Email: {{ $order->customer->email ?? '' }}<br>
                    Phone: {{ $order->customer->phone ?? '' }}<br>
                    Address: {{ $order->customer->address ?? 'N/A' }}
                </td>
                <td style="text-align: right;">
                    <span class="meta-label">Invoice Info</span>
                    Invoice Number: <strong>#{{ $order->invoice_number }}</strong><br>
                    Date: {{ $order->created_at->format('M d, Y') }}<br>
                    Payment Method: {{ strtoupper($order->payment_method) }}<br>
                    Payment Status: 
                    @if($order->status === 'paid')
                        <span class="badge badge-paid">PAID</span>
                    @elseif($order->status === 'payment_failed')
                        <span class="badge badge-failed">FAILED</span>
                    @else
                        <span class="badge badge-pending">{{ strtoupper($order->status) }}</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Store Information -->
        <table class="details-table">
            <tr>
                <td>
                    <span class="meta-label">Seller Details</span>
                    <strong>{{ $order->store->store_name ?? 'Southwest Farmers Store' }}</strong><br>
                    Address: {{ $order->store->address ?? 'Store Location' }}<br>
                    Phone: {{ $order->store->phone ?? 'N/A' }}
                </td>
                <td>
                    <!-- Right column empty for spacing -->
                </td>
            </tr>
        </table>

        <!-- Order Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product Description</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 20%;">Price</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->product_name }}</strong><br>
                            <small style="color: #64748b;">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->price, 2) }}</td>
                        <td class="text-right">${{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Totals -->
        <table class="summary-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="value">${{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount_amount > 0)
                <tr>
                    <td class="label">Discount</td>
                    <td class="value" style="color: #16a34a;">-${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td class="label">Shipping</td>
                <td class="value" style="color: #16a34a;">FREE</td>
            </tr>
            <tr class="grand-total">
                <td class="label">TOTAL PAID</td>
                <td class="value">${{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for shopping at Southwest Farmers!</p>
            <p style="font-size: 10px; margin-top: 5px;">This is a system generated document. No signature required.</p>
        </div>
    </div>
</body>
</html>
