<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advisory_consultations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('client_id')->constrained();

            // 見直しadvisory_id⇒advisory_contract_id
            $table->foreignId('advisory_contract_id')->nullable()->constrained();
            $table->foreignId('consultation_id')->nullable()->constrained();
            $table->tinyInteger('advisory_party');
            $table->string('title', 255);
            $table->tinyInteger('status');
            $table->tinyInteger('opponentconfliction')->nullable();
            $table->string('case_summary', 1000)->nullable();
            $table->string('special_notes', 1000)->nullable();
            $table->date('consultation_start_date')->nullable();
            $table->date('consultation_end_date')->nullable();
            $table->tinyInteger('close_reason')->nullable();
            $table->tinyInteger('office_id')->nullable();
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
            $table->foreignId('lawyer2_id')->nullable()->constrained('users');
            $table->foreignId('lawyer3_id')->nullable()->constrained('users');
            $table->foreignId('paralegal_id')->nullable()->constrained('users');
            $table->foreignId('paralegal2_id')->nullable()->constrained('users');
            $table->foreignId('paralegal3_id')->nullable()->constrained('users');
            $table->timestamps();

            // インデックス
            $table->index('client_id');
            $table->index('advisory_contract_id');
            $table->index('consultation_id');
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
        Schema::dropIfExists('advisory_consultations');
    }
};
