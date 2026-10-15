<!DOCTYPE html>
<html>
<head>
    <title>Store Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;">
    <div style="background-color: #fff; padding: 20px; border-radius: 5px; max-width: 600px; margin: auto;">
        <h2 style="color: #333;">{{ $forApprover ? 'Store Request Needs Your Approval' : 'Store Request Submitted' }}</h2>
        <p>
            {{ $r->store->store_name ?? 'A store' }} submitted a warehouse request
            <strong>{{ $r->request_number ?? '#' . $r->id }}</strong>
            ({{ $r->department->name ?? 'Department' }}).
            @unless ($forApprover) This is for your information; the Area Manager approves it. @endunless
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Product</th>
                <th style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">Qty</th>
            </tr>
            @foreach ($r->items as $item)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #eee;">{{ $item->product->product_name ?? 'Product' }}</td>
                    <td style="padding: 8px; border-bottom: 1px solid #eee; text-align: right;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </table>

        @if ($r->store_remarks)
            <p style="margin-top: 15px;"><strong>Remarks:</strong> {{ $r->store_remarks }}</p>
        @endif

        @if ($forApprover)
            <p style="margin-top: 25px; text-align: center;">
                <a href="{{ $approveUrl }}" style="background: #198754; color: #fff; padding: 12px 28px; border-radius: 4px; text-decoration: none; font-weight: bold;">Approve</a>
                &nbsp;
                <a href="{{ $rejectUrl }}" style="background: #dc3545; color: #fff; padding: 12px 28px; border-radius: 4px; text-decoration: none; font-weight: bold;">Reject</a>
            </p>
            <p style="color: #888; font-size: 12px; text-align: center;">These links work for 7 days.</p>
        @endif
    </div>
</body>
</html>
