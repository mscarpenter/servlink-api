<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles_establishment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('company_name')->unique();
            $table->string('cnpj', 18)->unique()->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->decimal('average_rating', 3, 2)->default(5.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles_establishment');
    }
};