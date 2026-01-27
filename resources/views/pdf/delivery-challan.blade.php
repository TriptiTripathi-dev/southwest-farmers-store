<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recall Challan #{{ $recall->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .title { font-size: 22px; font-weight: bold; text-transform: uppercase; color: #d32f2f; }
        .sub-title { font-size: 14px; color: #555; }
        
        .meta-table { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
        .meta-table td { vertical-align: top; width: 50%; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; }
        .box-title { font-weight: bold; text-decoration: underline; display: block; margin-bottom: 5px; }

        .info-row { margin-bottom: 20px; }
        .info-label { font-weight: bold; display: inline-block; width: 120px; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .items-table th { background-color: #eee; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .footer { margin-top: 60px; width: 100%; }
        .signature-box { float: right; width: 250px; text-align: center; border-top: 1px solid #000; padding-top: 10px; }
        
        .clear { clear: both; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Stock Return Note</div>
        <div class="sub-title">(Recall Challan)</div>
    </div>

    <div class="info-row">
        <div><span class="info-label">Challan No:</span> #RCL-{{ str_pad($recall->id, 5, '0', STR_PAD_LEFT) }}</div>
        <div><span class="info-label">Date:</span> {{ now()->format('d M Y, h:i A') }}</div>
        <div><span class="info-label">Status:</span> {{ strtoupper($recall->status) }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td>
                <span class="box-title">SENDER (STORE)</span><br>
                <strong>{{ $recall->store->store_name }}</strong><br>
                {{ $recall->store->store_address ?? 'Store Address' }}<br>
                {{ $recall->store->city ?? '' }}<br>
                <br>
                <strong>Initiated By:</strong> {{ $recall->initiator->name ?? 'Store Manager' }}
            </td>
            <td>
                <span class="box-title">RECEIVER (WAREHOUSE)</span><br>
                <strong>Central Warehouse</strong><br>
                Inbound Logistics Dept.<br>
                Returns Section<br>
            </td>
        </tr>
    </table>

    <h4 style="margin-bottom: 5px;">Item Details</h4>
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Product Description</th>
                <th width="20%">Reason</th>
                <th width="15%" class="text-center">Requested</th>
                <th width="15%" class="text-center">Dispatched</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>{{ $recall->product->product_name }}</strong><br>
                    <small>SKU: {{ $recall->product->sku }}</small><br>
                    <small>Category: {{ $recall->product->category->name ?? 'N/A' }}</small>
                </td>
                <td>
                    {{ ucwords(str_replace('_', ' ', $recall->reason)) }}<br>
                    @if($recall->reason_remarks)
                        <small><i>"{{ Str::limit($recall->reason_remarks, 30) }}"</i></small>
                    @endif
                </td>
                <td class="text-center">{{ $recall->requested_quantity }}</td>
                <td class="text-center" style="font-weight: bold; background-color: #f0f0f0;">
                    {{ $recall->dispatched_quantity }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Declaration:</strong> The goods listed above are being returned to the warehouse. Please acknowledge receipt.</p>
        
        <div class="signature-box">
            Authorized Signatory<br>
            (Store Manager)
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>