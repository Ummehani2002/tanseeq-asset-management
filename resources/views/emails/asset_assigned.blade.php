
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Asset Assignment Notification</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $employee->name ?? 'Employee' }}</strong>,</p>
            <p>An asset has been assigned to you. Please find the details below:</p>

            <table>
                <tr>
                    <td>Asset ID:</td>
                    <td>{{ $asset->asset_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Brand:</td>
                    <td>{{ $asset->brand ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Serial Number:</td>
                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>{{ $asset->category ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Issue Date:</td>
                    <td>{{ optional($transaction->issue_date)->toDateString() ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Remarks:</td>
                    <td>{{ $transaction->remarks ?? 'None' }}</td>
                </tr>
            </table>

            <p>If you have any questions, please contact the admin.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Asset Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>