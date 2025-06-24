<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advisory_contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('client_id')->constrained();
            $table->tinyInteger('advisory_party');
            $table->string('title', 255);
            $table->tinyInteger('status');
            $table->string('explanation', 1000)->nullable();
            $table->string('special_notes', 1000)->nullable();
            $table->date('advisory_start_date')->nullable();
            $table->date('advisory_end_date')->nullable();
            $table->integer('amount_monthly')->nullable();
            $table->integer('contract_term_monthly')->nullable();
            $table->date('consultation_firstdate')->nullable();
            $table->tinyInteger('payment_category')->nullable();
            $table->string('adviser_fee_auto', 255)->nullable();
            $table->tinyInteger('payment_method')->nullable();            
            $table->integer('withdrawal_request_amount')->nullable(); 
            $table->string('withdrawal_breakdown', 255)->nullable(); 
            $table->date('withdrawal_update_date')->nullable();                                  
            $table->tinyInteger('office_id')->nullable();
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
            $table->foreignId('lawyer2_id')->nullable()->constrained('users');
            $table->foreignId('lawyer3_id')->nullable()->constrained('users');
            $table->foreignId('paralegal_id')->nullable()->constrained('users');
            $table->foreignId('paralegal2_id')->nullable()->constrained('users');
            $table->foreignId('paralegal3_id')->nullable()->constrained('users');
            $table->tinyInteger('source')->nullable();
            $table->tinyInteger('source_detail')->nullable();
            $table->string('introducer_others', 255)->nullable();
            $table->string('gift', 255)->nullable();
            $table->tinyInteger('newyearscard')->nullable();
            $table->timestamps();

            // インデックス
            $table->index('client_id');
            $table->index('advisory_party');
            $table->index('title');
            $table->index('status');
            $table->index('lawyer_id');
            $table->index('lawyer2_id');
            $table->index('lawyer3_id');
            $table->index('paralegal_id');
            $table->index('paralegal2_id');
            $table->index('paralegal3_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('advisory_contracts');
    }
};
