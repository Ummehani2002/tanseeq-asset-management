
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #2e5d8fff; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .footer { background-color: #f8f9fa; padding: 15px; border-radius: 0 0 5px 5px; text-align: center; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
        table td:first-child { font-weight: bold; width: 30%; }
        .info-box { background-color: #e7f3ff; padding: 15px; border-left: 4px solid #2e5d8fff; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @php
                $transactionType = $transaction->transaction_type ?? 'assign';
                $title = match($transactionType) {
                    'assign' => 'Asset Assignment Notification',
                    'return' => 'Asset Return Notification',
                    'system_maintenance' => 'Asset Maintenance Notification',
                    default => 'Asset Transaction Notification',
                };
            @endphp
            <h2>{{ $title }}</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $employee->name ?? 'Employee' }}</strong>,</p>
            
            @if($transactionType === 'assign')
                <p>An asset has been assigned to you. Please find the details below:</p>
            @elseif($transactionType === 'return')
                <p>An asset has been returned. Please find the details below:</p>
            @elseif($transactionType === 'system_maintenance')
                <div class="info-box">
                    <p><strong>⚠️ Maintenance Notice:</strong> Your assigned asset has been sent for system maintenance.</p>
                </div>
                <p>Please find the maintenance details below:</p>
            @else
                <p>An asset transaction has been processed. Please find the details below:</p>
            @endif

            <table>
                <tr>
                    <td>Asset ID:</td>
                    <td>{{ $asset->asset_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>{{ $asset->assetCategory->category_name ?? ($asset->category ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td>Brand:</td>
                    <td>{{ $asset->brand->name ?? ($asset->brand ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td>Serial Number:</td>
                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                </tr>
                @if($transactionType === 'assign' && $transaction->issue_date)
                <tr>
                    <td>Issue Date:</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->issue_date)->format('Y-m-d') }}</td>
                </tr>
                @endif
                @if($transactionType === 'return' && $transaction->return_date)
                <tr>
                    <td>Return Date:</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->return_date)->format('Y-m-d') }}</td>
                </tr>
                @endif
                @if($transactionType === 'system_maintenance')
                    @if($transaction->receive_date)
                    <tr>
                        <td>Received for Maintenance:</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->receive_date)->format('Y-m-d') }}</td>
                    </tr>
                    @endif
                    @if($transaction->delivery_date)
                    <tr>
                        <td>Expected Delivery Date:</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->delivery_date)->format('Y-m-d') }}</td>
                    </tr>
                    @endif
                    @if($transaction->repair_type)
                    <tr>
                        <td>Repair Type:</td>
                        <td>{{ $transaction->repair_type }}</td>
                    </tr>
                    @endif
                @endif
                @if($transaction->remarks)
                <tr>
                    <td>Remarks:</td>
                    <td>{{ $transaction->remarks }}</td>
                </tr>
                @endif
            </table>

            @if($transactionType === 'system_maintenance')
                <div class="info-box">
                    <p><strong>Note:</strong> The asset will be returned to you once maintenance is complete. You will receive another notification when the asset is ready for collection.</p>
                </div>
            @endif

            <p>If you have any questions, please contact the admin.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Asset Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
