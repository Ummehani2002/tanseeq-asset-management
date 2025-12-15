<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Employee;
use App\Models\Asset;
use App\Models\AssetTransaction;

class AssetAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $asset;
    public $employee;
    public $transaction;

    public function __construct($asset, $employee, $transaction)
    {
        $this->asset = $asset;
        $this->employee = $employee;
        $this->transaction = $transaction;
    }

    public function build()
    {
        $transactionType = $this->transaction->transaction_type ?? 'assign';
        $subject = match($transactionType) {
            'assign' => 'Asset Assigned to You: ' . ($this->asset->asset_id ?? 'Asset'),
            'return' => 'Asset Return Notification: ' . ($this->asset->asset_id ?? 'Asset'),
            'system_maintenance' => 'Asset Maintenance Notification: ' . ($this->asset->asset_id ?? 'Asset'),
            default => 'Asset Transaction Notification: ' . ($this->asset->asset_id ?? 'Asset'),
        };

        return $this->subject($subject)
                    ->view('emails.asset_assigned')
                    ->with([
                        'asset' => $this->asset,
                        'employee' => $this->employee,
                        'transaction' => $this->transaction
                    ]);
    }
}
