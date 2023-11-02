<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\AirportPickupEmailForQueuing;
use App\Mail\DoctorApmtEmailForQueuing;
use App\Mail\HotelBookingEmailForQueuing;
use App\Mail\SmoEmailForQueuing;
use App\Mail\TeleMedicineEmailForQueuing;
use App\Mail\VisaRequestEmailForQueuing;
use Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    
    public function __construct($details)
    {
        $this->details = $details;
    }

    public function handle()
    {
        if ($this->details['TYPE'] == 'DA') {
            $email = new DoctorApmtEmailForQueuing($this->details);
        }
        if ($this->details['TYPE'] == 'SM') {
            $email = new SmoEmailForQueuing($this->details);
        }
        if ($this->details['TYPE'] == 'AP') {
            $email = new AirportPickupEmailForQueuing($this->details);
        }
        if ($this->details['TYPE'] == 'HB') {
            $email = new HotelBookingEmailForQueuing($this->details);
        }
        if ($this->details['TYPE'] == 'VR') {
            $email = new VisaRequestEmailForQueuing($this->details);
        }
        if ($this->details['TYPE'] == 'TM') {
            $email = new TeleMedicineEmailForQueuing($this->details);
        }
        Mail::to($this->details['hospitalinfo'])->send($email);
    }
}
