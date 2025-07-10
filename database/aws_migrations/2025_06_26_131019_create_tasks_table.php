<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('related_party');

            // 外部キー
            $table->foreignId('consultation_id')->nullable()->constrained();
            $table->foreignId('business_id')->nullable()->constrained();
            $table->foreignId('advisory_contract_id')->nullable()->constrained();
            $table->foreignId('advisory_consultation_id')->nullable()->constrained();

            $table->tinyInteger('record1');
            $table->tinyInteger('record2');
            $table->string('title', 255);
            $table->tinyInteger('status');
            $table->tinyInteger('already_read')->nullable();
            $table->date('record_date')->nullable();
            $table->date('deadline_date')->nullable();
            $table->time('deadline_time')->nullable();
            $table->string('content', 1000)->nullable();
            $table->foreignId('orderer_id')->nullable()->constrained('users');
            $table->foreignId('worker_id')->nullable()->constrained('users');
            $table->string('attachment1_title', 255)->nullable();
            $table->string('attachment2_title', 255)->nullable();
            $table->string('attachment3_title', 255)->nullable();
            $table->text('link1')->nullable();
            $table->text('link2')->nullable();
            $table->text('link3')->nullable();
            $table->tinyInteger('carrier')->nullable();
            $table->string('tracking_number', 255)->nullable(); 
            $table->tinyInteger('phone_request')->nullable();
            $table->tinyInteger('notify_type')->nullable();
            $table->string('record_to', 255)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('phone_to', 15)->nullable();
            $table->string('phone_from', 15)->nullable();
            $table->string('naisen_to', 15)->nullable();
            $table->string('naisen_from', 15)->nullable();
            $table->tinyInteger('notify_person_in')->nullable();
            $table->timestamps();


            // インデックス
            $table->index('related_party');
            $table->index('consultation_id');
            $table->index('business_id');
            $table->index('advisory_contract_id');
            $table->index('advisory_consultation_id');
            $table->index('title');
            $table->index('status');
            $table->index('record1');
            $table->index('record2');
            $table->index('orderer_id');
            $table->index('worker_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
