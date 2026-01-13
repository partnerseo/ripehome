<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string',
            ], [
                'name.required' => 'Ad Soyad alanı zorunludur',
                'email.required' => 'E-posta alanı zorunludur',
                'email.email' => 'Geçerli bir e-posta adresi giriniz',
                'message.required' => 'Mesaj alanı zorunludur',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen tüm alanları doldurunuz',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contact = ContactMessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.',
                'data' => [
                    'id' => $contact->id,
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Mesaj gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyiniz.'
            ], 500);
        }
    }
}













