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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('client_identity');
            /***
            Assuming contact_point from input structure can be split into address and country
            eg. '123 Enigma Ave, Bletchley Park, UK' splits into:  
            client_address = '123 Enigma Ave, Bletchley Park',
            client_country = 'UK'
            ***/
            $table->string('client_address');
            $table->string('client_country');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
