<?php

namespace App\Providers;

use App\Models\Setting;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadSmtpSettings();
        $this->configureDefaults();
    }

    /**
     * Load SMTP settings saved in the database into runtime mail config.
     */
    protected function loadSmtpSettings(): void
    {
        if (! Schema::hasTable((new Setting())->getTable())) {
            return;
        }

        $smtpSettings = Setting::query()
            ->whereIn('key', [
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'personnel_onboarding_cc_address',
                'personnel_onboarding_cc_name',
            ])
            ->pluck('value', 'key');

        if ($smtpSettings->isEmpty()) {
            return;
        }

        config([
            'mail.mailers.smtp.host' => $smtpSettings->get('smtp_host', config('mail.mailers.smtp.host')),
            'mail.mailers.smtp.port' => $smtpSettings->has('smtp_port')
                ? (int) $smtpSettings->get('smtp_port')
                : config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => $smtpSettings->get('smtp_username', config('mail.mailers.smtp.username')),
            'mail.mailers.smtp.password' => $smtpSettings->get('smtp_password', config('mail.mailers.smtp.password')),
            'mail.mailers.smtp.encryption' => $smtpSettings->get('smtp_encryption') ?: null,
            'mail.personnel_onboarding_cc.address' => $smtpSettings->get('personnel_onboarding_cc_address', config('mail.personnel_onboarding_cc.address')),
            'mail.personnel_onboarding_cc.name' => $smtpSettings->get('personnel_onboarding_cc_name', config('mail.personnel_onboarding_cc.name')),
        ]);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
