<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Challan #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #4B49AC; }
        .challan-title { float: right; font-size: 20px; font-weight: bold; text-transform: uppercase; color: #555; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; padding: 5px; }
        .label { font-weight: bold; color: #777; width: 120px; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f8f9fa; color: #333; }
        .items-table td { font-size: 13px; }

        .footer { margin-top: 50px; width: 100%; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #ddd; padding-top: 20px; }
        .signatures { width: 100%; margin-top: 60px; }
        .signature-box { float: left; width: 33%; text-align: center; }
        .line { border-top: 1px solid #333; width: 80%; margin: 0 auto 5px auto; }
    </style>
</head>
<body>

    <div class="header">
        <span class="logo">{{ config('app.name', 'Inventory System') }}</span>
        <span class="challan-title">Delivery Challan</span>
        <div style="clear: both;"></div>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>From (Warehouse):</strong><br>
                Central Warehouse<br>
                123 Logistics Park, Industrial Area<br>
                New Delhi, India - 110020
            </td>
            <td width="50%">
                <strong>To (Store):</strong><br>
                {{ $request->store->store_name }}<br>
                {{ $request->store->store_address ?? 'Address not available' }}<br>
                {{ $request->store->city ?? '' }}
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td><span class="label">Challan No:</span> #DC-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td><span class="label">Reference Req:</span> #REQ-{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td><span class="label">Date:</span> {{ now()->format('d M Y') }}</td>
            <td><span class="label">Status:</span> {{ strtoupper($request->status) }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="40%">Item Description</th>
                <th width="20%">SKU</th>
                <th width="15%">Category</th>
                <th width="20%" style="text-align: center;">Quantity Dispatched</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $request->product->product_name }}</td>
                <td>{{ $request->product->sku }}</td>
                <td>{{ $request->product->category->name ?? 'General' }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $request->fulfilled_quantity }}</td>
            </tr>
            </tbody>
    </table>

    <div class="signatures">
        <div class="signature-box">
            <div class="line"></div>
            Prepared By
        </div>
        <div class="signature-box">
            <div class="line"></div>
            Authorized By
        </div>
        <div class="signature-box">
            <div class="line"></div>
            Received By
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature required.</p>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

</body>
</html>