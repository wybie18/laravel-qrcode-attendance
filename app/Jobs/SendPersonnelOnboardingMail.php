<?php

namespace App\Jobs;

use App\Mail\PersonnelOnboardingMail;
use App\Models\Personnel;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPersonnelOnboardingMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Personnel $personnel,
        private string $qrImage,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $settings = Setting::whereIn('key', [
            'smtp_host', 'smtp_port', 'smtp_username',
            'smtp_password', 'smtp_encryption',
        ])->pluck('value', 'key');

        config([
            'mail.mailers.smtp.host'       => $settings->get('smtp_host'),
            'mail.mailers.smtp.port'       => (int) $settings->get('smtp_port'),
            'mail.mailers.smtp.username'   => $settings->get('smtp_username'),
            'mail.mailers.smtp.password'   => $settings->get('smtp_password'),
            'mail.mailers.smtp.encryption' => $settings->get('smtp_encryption') ?: null,
            'mail.from.address'            => $settings->get('smtp_username'),
        ]);

        app('mail.manager')->purge('smtp');

        Mail::to($this->personnel->email)
            ->send(new PersonnelOnboardingMail($this->personnel, $this->qrImage));
    }
}
