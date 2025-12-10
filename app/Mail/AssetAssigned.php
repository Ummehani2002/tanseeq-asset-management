<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Employee;
use App\Models\Asset;

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
        return $this->subject('Asset Assigned to You: ' . ($this->asset->asset_id ?? 'Asset'))
                    ->view('emails.asset_assigned')
                    ->with([
                        'asset' => $this->asset,
                        'employee' => $this->employee,
                        'transaction' => $this->transaction
                    ]);
    }
}