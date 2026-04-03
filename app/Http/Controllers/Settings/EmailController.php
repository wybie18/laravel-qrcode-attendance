<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\EmailSettingsUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class EmailController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('settings/Email', [
            'smtp_host' => $this->getSettingValue('smtp_host'),
            'smtp_port' => $this->getSettingValue('smtp_port'),
            'smtp_username' => $this->getSettingValue('smtp_username'),
            'smtp_password' => $this->getSettingValue('smtp_password'),
            'smtp_encryption' => $this->getSettingValue('smtp_encryption'),
            'personnel_onboarding_cc_address' => $this->getSettingValue('personnel_onboarding_cc_address'),
            'personnel_onboarding_cc_name' => $this->getSettingValue('personnel_onboarding_cc_name'),
        ]);
    }

    public function update(EmailSettingsUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        config([
            'mail.mailers.smtp.host' => $validated['smtp_host'],
            'mail.mailers.smtp.port' => (int) $validated['smtp_port'],
            'mail.mailers.smtp.username' => $validated['smtp_username'],
            'mail.mailers.smtp.password' => $validated['smtp_password'],
            'mail.mailers.smtp.encryption' => $validated['smtp_encryption'] ?: null,
            'mail.personnel_onboarding_cc.address' => $validated['personnel_onboarding_cc_address'],
            'mail.personnel_onboarding_cc.name' => $validated['personnel_onboarding_cc_name'],

            'mail.from.address' => $validated['smtp_username'],
        ]);

        app('mail.manager')->purge('smtp');
        Artisan::call('queue:restart');

        return to_route('email.edit')->with('success', 'SMTP settings saved successfully.');
    }

    private function getSettingValue(string $key): ?string
    {
        return Setting::query()
            ->where('key', $key)
            ->first()
            ?->value;
    }
}
