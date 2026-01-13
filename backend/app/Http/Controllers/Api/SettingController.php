<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        try {
            $setting = Setting::first();

            if (!$setting) {
                return response()->json([
                    'success' => true,
                    'data' => null
                ]);
            }

            $data = [
                'logo' => ImageHelper::getStorageUrl($setting->logo),
                'favicon' => ImageHelper::getStorageUrl($setting->favicon),
                'phone' => $setting->phone,
                'email' => $setting->email,
                'address' => $setting->address,
                'social_media' => [
                    'facebook' => $setting->facebook,
                    'instagram' => $setting->instagram,
                    'twitter' => $setting->twitter,
                    'linkedin' => $setting->linkedin,
                ],
                'footer_text' => $setting->footer_text,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Settings fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar yüklenirken hata oluştu'
            ], 500);
        }
    }
}












