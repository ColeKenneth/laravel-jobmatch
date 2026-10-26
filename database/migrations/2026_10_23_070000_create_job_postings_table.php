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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->onDelete('cascade');
            $table->string('title', 50);
            $table->text('description');
            $table->string('requirements', 150);
            $table->string('preferred_skills', 150)->nullable();
            $table->integer('min_experience_years');
            $table->decimal('min_salary', 9, 2)->nullable();
            $table->decimal('max_salary', 9, 2)->nullable();
            $table->string('location', 100);
            $table->string('employment_type', 20);
            $table->dateTime('posted_date');
            $table->dateTime('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('views')->default(0);
            $table->integer('applications')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
