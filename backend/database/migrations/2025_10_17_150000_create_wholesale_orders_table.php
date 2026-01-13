<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wholesale_orders', function (Blueprint $table) {
            $table->id();
            
            // Müşteri/Şirket Bilgileri
            $table->string('company_name'); // Şirket adı
            $table->string('contact_person'); // Yetkili kişi adı
            $table->string('email');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('tax_office')->nullable(); // Vergi dairesi
            $table->string('tax_number')->nullable(); // Vergi no
            
            // Sipariş İçeriği
            $table->json('items'); // [{product_id, product_name, product_image, quantity, notes}]
            $table->text('additional_notes')->nullable(); // Genel notlar
            
            // Durum Yönetimi
            $table->enum('status', [
                'pending',      // Yeni Talep
                'reviewing',    // İnceleniyor
                'approved',     // Onaylandı
                'rejected',     // Reddedildi
                'completed'     // Tamamlandı
            ])->default('pending');
            
            $table->text('admin_notes')->nullable(); // Admin notu
            $table->timestamp('reviewed_at')->nullable(); // İnceleme tarihi
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wholesale_orders');
    }
};





