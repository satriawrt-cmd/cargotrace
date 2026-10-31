<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            [
                'name' => 'Maersk Line',
                'code' => 'MAEU',
                'contact_email' => 'dispatch@maersk.com',
                'contact_phone' => '+45-3363-3363',
                'service_type' => 'SEA_FREIGHT',
                'tracking_url_template' => 'https://www.maersk.com/tracking/{code}',
                'is_active' => true,
            ],
            [
                'name' => 'MSC Mediterranean Shipping',
                'code' => 'MSCU',
                'contact_email' => 'operations@msc.com',
                'contact_phone' => '+41-22-703-8888',
                'service_type' => 'SEA_FREIGHT',
                'tracking_url_template' => 'https://www.msc.com/track-a-shipment?query={code}',
                'is_active' => true,
            ],
            [
                'name' => 'CMA CGM Group',
                'code' => 'CMDU',
                'contact_email' => 'support@cma-cgm.com',
                'contact_phone' => '+33-4-88-91-90-00',
                'service_type' => 'SEA_FREIGHT',
                'tracking_url_template' => 'https://www.cma-cgm.com/ebusiness/tracking/search?SearchBy=BL&Reference={code}',
                'is_active' => true,
            ],
            [
                'name' => 'Hapag-Lloyd',
                'code' => 'HLCU',
                'contact_email' => 'service@hlag.com',
                'contact_phone' => '+49-40-3001-0',
                'service_type' => 'SEA_FREIGHT',
                'tracking_url_template' => 'https://www.hapag-lloyd.com/en/online-business/track/track-by-booking-solution.html?blno={code}',
                'is_active' => true,
            ],
            [
                'name' => 'DHL Global Forwarding',
                'code' => 'DHLG',
                'contact_email' => 'track@dhl.com',
                'contact_phone' => '+49-228-182-0',
                'service_type' => 'MULTIMODAL',
                'tracking_url_template' => 'https://www.dhl.com/global-en/home/tracking.html?tracking-id={code}',
                'is_active' => true,
            ],
            [
                'name' => 'Emirates SkyCargo',
                'code' => 'EKAC',
                'contact_email' => 'skycargo@emirates.com',
                'contact_phone' => '+971-4-214-4444',
                'service_type' => 'AIR_FREIGHT',
                'tracking_url_template' => 'https://www.skycargo.com/services/track-shipment/?airwaybill={code}',
                'is_active' => true,
            ],
        ];

        foreach ($carriers as $carrier) {
            Carrier::updateOrCreate(['code' => $carrier['code']], $carrier);
        }
    }
}
