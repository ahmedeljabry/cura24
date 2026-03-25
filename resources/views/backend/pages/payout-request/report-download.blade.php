<!DOCTYPE html>
<html>
<head>
    <title>Pending Payout Requests Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Pending Payout Requests Report</h2>
    <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Seller Name</th>
                <th>Email</th>
                <th>Amount</th>
                <th>Payment Gateway</th>
                <th>Payment Receipt</th>
                <th>Seller Note</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payout_requests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->seller->name ?? 'N/A' }}</td>
                    <td>{{ $request->seller->email ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($request->amount, 2) }}</td>
                    <td>{{ $request->payment_gateway ?? 'N/A' }}</td>
                    <td>{{ $request->payment_receipt ?? 'N/A' }}</td>
                    <td>{{ $request->seller_note ?? 'N/A' }}</td>
                    <td>{{ $request->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No pending payout requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>