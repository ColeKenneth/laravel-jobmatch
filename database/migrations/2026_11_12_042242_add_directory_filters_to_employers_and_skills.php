<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->string('location', 100)->nullable();
            $table->string('company_size', 20)->nullable();
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->string('discipline', 40)->nullable();
            $table->string('source', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employers', fn (Blueprint $table) => $table->dropColumn(['location', 'company_size']));
        Schema::table('skills', fn (Blueprint $table) => $table->dropColumn(['discipline', 'source']));
    }
};
