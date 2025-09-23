<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                 // e.g. CEY-20-ABC12
            $table->enum('type', ['percent', 'flat']);        // percent | flat
            $table->decimal('amount', 8, 2);                  // 20 | 15.00
            $table->decimal('min', 8, 2)->default(0);         // minimum order amount
            $table->timestamp('expires_at')->nullable();      // optional expiry
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
