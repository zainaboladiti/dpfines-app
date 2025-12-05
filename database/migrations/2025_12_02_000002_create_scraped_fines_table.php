<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scraped_fines', function (Blueprint $table) {
            $table->id();
            $table->string('organisation');
            $table->string('regulator')->nullable();
            $table->string('sector')->nullable();
            $table->string('region')->nullable();
            $table->decimal('fine_amount', 15, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('fine_date')->nullable();
            $table->string('law')->nullable();
            $table->text('articles_breached')->nullable();
            $table->string('violation_type')->nullable();
            $table->longText('summary')->nullable();
            $table->string('badges')->nullable();
            $table->string('link_to_case')->nullable();

            // Review workflow fields
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            // Audit fields
            $table->unsignedBigInteger('submitted_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->index('status');
            $table->index('submitted_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraped_fines');
    }
};
