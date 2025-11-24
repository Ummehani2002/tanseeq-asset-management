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

    public $employee;
    public $asset;

    public function __construct(Employee $employee, Asset $asset)
    {
        $this->employee = $employee;
        $this->asset = $asset;
    }

    public function build()
    {
        return $this->subject('Asset Assignment Notification')
                    ->view('emails.asset_assigned');
    }
}