<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

class TranslationHelper
{
    /**
     * Map of status codes to human-readable strings.
     */
    protected static array $statusMap = [
        'id' => [
            'CREATED' => 'Dibuat',
            'BOOKED' => 'Terjadwal',
            'PICKED_UP' => 'Kargo Diambil',
            'IN_TRANSIT' => 'Dalam Perjalanan',
            'DELIVERED' => 'Terkirim',
            'CANCELLED' => 'Dibatalkan',
        ],
        'en' => [
            'CREATED' => 'Created',
            'BOOKED' => 'Booked',
            'PICKED_UP' => 'Picked Up',
            'IN_TRANSIT' => 'In Transit',
            'DELIVERED' => 'Delivered',
            'CANCELLED' => 'Cancelled',
        ],
    ];

    /**
     * Map of delay statuses.
     */
    protected static array $delayStatusMap = [
        'id' => [
            'ON_TIME' => 'Tepat Waktu',
            'ON_SCHEDULE' => 'Tepat Waktu',
            'DELAYED' => 'Terlambat',
        ],
        'en' => [
            'ON_TIME' => 'On Time',
            'ON_SCHEDULE' => 'On Schedule',
            'DELAYED' => 'Delayed',
        ],
    ];

    /**
     * Map of exception types.
     */
    protected static array $exceptionTypeMap = [
        'id' => [
            'CUSTOMS_HOLD' => 'Penahanan Bea Cukai',
            'WEATHER_DELAY' => 'Kendala Cuaca Buruk',
            'PORT_CONGESTION' => 'Kepadatan Pelabuhan',
            'DAMAGED' => 'Kerusakan Muatan',
            'MISROUTED' => 'Salah Rute Pengiriman',
            'DOCUMENTATION_ISSUE' => 'Kendala Dokumen',
        ],
        'en' => [
            'CUSTOMS_HOLD' => 'Customs Hold',
            'WEATHER_DELAY' => 'Weather Delay',
            'PORT_CONGESTION' => 'Port Congestion',
            'DAMAGED' => 'Cargo Damaged',
            'MISROUTED' => 'Misrouted',
            'DOCUMENTATION_ISSUE' => 'Documentation Issue',
        ],
    ];

    /**
     * Map of severity levels.
     */
    protected static array $severityMap = [
        'id' => [
            'NONE' => 'Normal',
            'LOW' => 'Rendah (0-6j)',
            'MEDIUM' => 'Sedang (6-24j)',
            'HIGH' => 'Tinggi (>24j)',
            'CRITICAL' => 'Kritis',
        ],
        'en' => [
            'NONE' => 'None',
            'LOW' => 'Low (0-6h)',
            'MEDIUM' => 'Medium (6-24h)',
            'HIGH' => 'High (>24h)',
            'CRITICAL' => 'Critical',
        ],
    ];

    /**
     * Map of phrases and descriptions to Indonesian.
     */
    protected static array $textMap = [
        // Event descriptions
        'Booking confirmed and export documentation lodged at Shanghai Port.' => 'Pemesanan dikonfirmasi dan dokumen ekspor diserahkan di Pelabuhan Shanghai.',
        'Container sealed and loaded onto vessel Maersk Mc-Kinney Moller.' => 'Kontainer disegel dan dimuat ke atas kapal Maersk Mc-Kinney Moller.',
        'Bunkering stop and maritime corridor clearance at Singapore anchorage.' => 'Pemberhentian pengisian bahan bakar dan izin koridor maritim di dermaga Singapura.',
        'Vessel successfully queued and entered Northbound Suez Canal transit convoy.' => 'Kapal berhasil mengantre dan memasuki konvoi transit Terusan Suez arah utara.',
        'Cold chain container plugged into power grid at PSA Singapore Terminal.' => 'Kontainer rantai dingin terhubung ke jaringan listrik di Terminal PSA Singapura.',
        'Vessel on course towards Torres Strait.' => 'Kapal berada di jalur pelayaran menuju Selat Torres.',
        '[EXCEPTION: CUSTOMS_HOLD] Australian Department of Agriculture biosecurity hold on cold-chain manifests.' => '[KENDALA: PENAHANAN BEA CUKAI] Penahanan biosekuriti Departemen Pertanian Australia pada manifes rantai dingin.',
        'Containers gated in and scheduled for loading on MSC Isabella voyage 2603.' => 'Kontainer masuk gerbang pelabuhan dan dijadwalkan dimuat ke pelayaran MSC Isabella 2603.',
        'Cargo loaded onboard Hapag-Lloyd vessel at Santos terminal.' => 'Kargo dimuat ke atas kapal Hapag-Lloyd di terminal Santos.',
        'Mid-Atlantic navigation corridor completed without anomalies.' => 'Koridor navigasi Mid-Atlantik selesai dilalui tanpa anomali.',
        'Consignment successfully discharged, customs cleared, and signed off by consignee.' => 'Kargo berhasil dibongkar, lolos pemeriksaan bea cukai, dan diserahterimakan ke penerima barang.',
        'Export inspection cleared and container stowed on container deck at Jebel Ali.' => 'Pemeriksaan ekspor selesai dan kontainer disimpan di dek kontainer di Jebel Ali.',
        '[EXCEPTION: PORT_CONGESTION] Berth queue congested due to prior typhoon backlog.' => '[KENDALA: KEPADATAN PELABUHAN] Antrean dermaga padat akibat penumpukan pasca badai topan.',
        'Transit resumed on schedule after terminal clearance.' => 'Perjalanan dilanjutkan sesuai jadwal setelah penyelesaian izin terminal.',

        // Locations
        'Atlantic Ocean Crossing' => 'Penyeberangan Samudra Atlantik',
        'South China Sea / Singapore Strait' => 'Laut Cina Selatan / Selat Singapura',
        'Coral Sea Approach / Port of Sydney outer anchorage' => 'Pendekatan Laut Koral / Dermaga Luar Pelabuhan Sydney',
        'Shanghai Waigaoqiao Terminal' => 'Terminal Waigaoqiao Shanghai',

        // Cargo Types
        'Consumer Electronics & Solar Panels' => 'Elektronik Konsumen & Panel Surya',
        'Pharmaceutical Vaccines & Cold Chain Supplies' => 'Vaksin Farmasi & Pasokan Rantai Dingin',
        'Textile Garments & Footwear' => 'Pakaian Tekstil & Alas Kaki',
        'FSC Certified Bleached Eucalyptus Pulp' => 'Pulp Kayu Putih Dikelantang Bersertifikat FSC',
        'Telecommunications Fiber Optics' => 'Kabel Fiber Optik Telekomunikasi',
        'Automotive Spare Parts & EV Batteries' => 'Suku Cadang Otomotif & Baterai Mobil Listrik',

        // Manifest items
        'Industrial Solar Inverters & Cells' => 'Inverter & Sel Surya Industri',
        'Smart Home Control Panels' => 'Panel Kendali Rumah Pintar',
        'Temperature-monitored Vaccine Vials' => 'Vial Vaksin dengan Pemantau Suhu',
        'Woven Cotton Shirts & Trousers' => 'Kemeja & Celana Katun Tenun',
        'Leather Casual Shoes & Sneakers' => 'Sepatu Santai & Sepatu Olahraga Kulit',
        'High-purity Eucalyptus Cellulose Sheets' => 'Lembaran Selulosa Kayu Putih Kemurnian Tinggi',
        'Optic Fiber Cables (Single Mode, Armored)' => 'Kabel Fiber Optik (Single Mode, Lapis Baja)',
        'Lithium-ion Battery Modules (48V)' => 'Modul Baterai Litium-ion (48V)',
        'Regenerative Braking ECUs' => 'ECU Pengereman Regeneratif',

        // Packaging
        'Pallet' => 'Palet',
        'Box' => 'Dus / Kotak',
        'Container' => 'Kontainer',
        'Drum' => 'Drum',
        'Crate' => 'Peti Kayu',

        // Reasons & Instructions
        'Delay of 18h due to severe tropical swell and mandatory customs biosecurity inspection.' => 'Keterlambatan 18 jam akibat gelombang pasang tropis dan inspeksi wajib biosekuriti bea cukai.',
        'Maintain temperature-controlled container at 20°C. High value lithium equipment.' => 'Jaga suhu kontainer terkontrol pada 20°C. Peralatan litium bernilai tinggi.',
        'Continuous temperature logger verification required upon Australian biosecurity clearance.' => 'Verifikasi berkala pencatat suhu diperlukan saat pemeriksaan biosekuriti Australia.',
        'Severe rolling swell in Great Australian Bight and mandatory quarantine document hold.' => 'Gelombang besar di Great Australian Bight dan penahanan dokumen karantina wajib.',
        'Prior tropical storm created berth queue in Port of Hamburg.' => 'Badai tropis sebelumnya menyebabkan antrean sandar di Pelabuhan Hamburg.',
    ];

    /**
     * Weather translations
     */
    protected static array $weatherMap = [
        'Partly Cloudy' => 'Sebagian Berawan',
        'Clear Sky' => 'Langit Cerah',
        'Tropical Fair' => 'Cerah Tropis',
        'Overcast' => 'Mendung',
        'Rain' => 'Hujan',
        'Light Rain' => 'Hujan Ringan',
        'Passing Showers' => 'Hujan Lokal',
        'Fair' => 'Cerah',
        'Sunny' => 'Terang Benderang',
    ];

    public static function translateStatus(?string $status): string
    {
        if (!$status) return '';
        $locale = App::getLocale() ?: session('locale', 'id');
        return self::$statusMap[$locale][$status] ?? str_replace('_', ' ', $status);
    }

    public static function translateDelayStatus(?string $status): string
    {
        if (!$status) return '';
        $locale = App::getLocale() ?: session('locale', 'id');
        return self::$delayStatusMap[$locale][$status] ?? str_replace('_', ' ', $status);
    }

    public static function translateExceptionType(?string $type): string
    {
        if (!$type) return '';
        $locale = App::getLocale() ?: session('locale', 'id');
        return self::$exceptionTypeMap[$locale][$type] ?? str_replace('_', ' ', $type);
    }

    public static function translateSeverity(?string $sev): string
    {
        if (!$sev) return '';
        $locale = App::getLocale() ?: session('locale', 'id');
        return self::$severityMap[$locale][$sev] ?? $sev;
    }

    public static function translateText(?string $text): string
    {
        if (!$text) return '';
        $locale = App::getLocale() ?: session('locale', 'id');
        if ($locale !== 'id') {
            return $text;
        }

        // Direct match
        if (isset(self::$textMap[$text])) {
            return self::$textMap[$text];
        }

        // Partial match / weather replacement
        $result = $text;
        foreach (self::$weatherMap as $en => $id) {
            $result = str_ireplace($en, $id, $result);
        }

        return $result;
    }

    public static function translateWeather(?string $weather): string
    {
        if (!$weather) return '';
        $locale = App::getLocale() ?: session('locale', 'id');
        if ($locale !== 'id') {
            return $weather;
        }

        $result = $weather;
        foreach (self::$weatherMap as $en => $id) {
            $result = str_ireplace($en, $id, $result);
        }

        return $result;
    }
}
