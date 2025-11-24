<h2>Asset Assignment Notification</h2>

<p>Dear {{ $employee->name }},</p>

<p>The following asset has been assigned to you:</p>

<ul>
    <li><strong>Asset ID:</strong> {{ $asset->asset_id }}</li>
    <li><strong>Asset Category:</strong> {{ $asset->assetCategory->category_name ?? 'N/A' }}</li>
    <li><strong>Asset Brand:</strong> {{ $asset->brand->name ?? 'N/A' }}</li>
</ul>

<p>Thank you,<br>Admin</p>
<p> Please contact the IT department if you have any questions regarding this asset assignment.</p>
