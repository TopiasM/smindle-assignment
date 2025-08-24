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
        Schema::create('api_calls', function (Blueprint $table) {
            $table->id();
            $table->string('request_url');
            $table->longText('response_body')->nullable();
            $table->smallinteger('status_code');
            $table->float('response_time', precision: 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_calls');
    }
};
