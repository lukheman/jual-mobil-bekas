<?php

namespace App\Http\Controllers\Guest;

use App\Enums\BodyType;
use App\Enums\LeadStatus;
use App\Enums\LeadType;
use App\Enums\Transmission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\SellCarRequest;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellCarController extends Controller
{
    /**
     * Display the sell-your-car page.
     */
    public function create(): View
    {
        return view('guest.sell-car', [
            'settings' => Setting::query()->pluck('value', 'key'),
            'transmissions' => Transmission::cases(),
            'bodyTypes' => BodyType::cases(),
        ]);
    }

    /**
     * Store the seller's car information and continue to WhatsApp.
     */
    public function store(SellCarRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $settings = Setting::query()->pluck('value', 'key');
        $siteName = $settings['site_name'] ?? 'RodaTa';
        $message = $this->whatsappMessage($data, $siteName);

        Lead::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'type' => LeadType::Sell,
            'message' => $message,
            'status' => LeadStatus::New,
            'consent_at' => now(),
        ]);

        return redirect()->away(
            'https://wa.me/'.$this->whatsappNumber($settings['whatsapp_number'] ?? null).'?text='.rawurlencode($message)
        );
    }

    /**
     * Build the WhatsApp message from the submitted car information.
     *
     * @param  array<string, mixed>  $data
     */
    private function whatsappMessage(array $data, string $siteName): string
    {
        $lines = [
            "Halo {$siteName}, saya ingin menjual mobil.",
            '',
            'Nama: '.$data['name'],
            'WhatsApp: '.$data['phone'],
        ];

        if (! empty($data['email'])) {
            $lines[] = 'Email: '.$data['email'];
        }

        $lines[] = 'Merek: '.$data['brand'];
        $lines[] = 'Model: '.$data['model'];
        $lines[] = 'Tahun: '.$data['year'];

        if (! empty($data['transmission'])) {
            $lines[] = 'Transmisi: '.Transmission::from($data['transmission'])->label();
        }

        if (! empty($data['body_type'])) {
            $lines[] = 'Tipe bodi: '.BodyType::from($data['body_type'])->label();
        }

        if (isset($data['mileage'])) {
            $lines[] = 'Jarak tempuh: '.number_format((int) $data['mileage'], 0, ',', '.').' km';
        }

        if (isset($data['expected_price'])) {
            $lines[] = 'Harga yang diharapkan: Rp '.number_format((int) $data['expected_price'], 0, ',', '.');
        }

        if (! empty($data['condition'])) {
            $lines[] = '';
            $lines[] = 'Kondisi: '.$data['condition'];
        }

        $lines[] = '';
        $lines[] = 'Mohon dibantu untuk proses penilaian dan penjualan. Terima kasih.';

        return implode("\n", $lines);
    }

    /**
     * Normalize the showroom WhatsApp number for wa.me.
     */
    private function whatsappNumber(?string $number): string
    {
        $number = preg_replace('/\D+/', '', (string) $number);

        if ($number === '') {
            return '6281234567890';
        }

        if (str_starts_with($number, '0')) {
            return '62'.substr($number, 1);
        }

        return $number;
    }
}
