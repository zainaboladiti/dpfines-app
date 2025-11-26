<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('global_fines', function (Blueprint $table) {
        $table->id();
        $table->string('organisation');
        $table->enum('regulator', [
            'ICO (UK)','CNIL (France)','BfDI (Germany)','DPC (Ireland)','AEPD (Spain)',
            'FTC (USA)','OAIC (Australia)','OPC (Canada)','CNPD (Luxembourg)'
        ]);
        $table->enum('sector', [
            'Finance & Banking','Healthcare','Technology','Retail & E-commerce','Telecommunications',
            'Public Sector','Education','Aviation / Transportation','Social Media'
        ]);
        $table->enum('region', ['EU / EEA','USA','Australia','Canada','Global'])->nullable();
        $table->decimal('fine_amount', 20, 2)->nullable();
        $table->enum('currency', ['EUR','GBP','USD','AUD','CAD']);
        $table->date('fine_date')->nullable();
        $table->enum('law', ['GDPR','UK GDPR','DPA 2018','CCPA','Other'])->nullable();
        $table->string('articles_breached')->nullable();
        $table->enum('violation_type', [
            'Security Breach','Inadequate Security','Consent Issues','Transparency',
            'Data Transfer','Unlawful Processing','Childrens Privacy'
        ])->nullable();
        $table->text('summary')->nullable();
        $table->string('badges')->nullable();
        $table->text('link_to_case');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_fines');
    }
};
