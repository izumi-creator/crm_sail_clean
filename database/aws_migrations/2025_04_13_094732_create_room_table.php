<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_name')->unique();
            $table->string('calendar_id')->unique(); // GoogleカレンダーのID
            $table->unsignedTinyInteger('office_id')->comment('所属事務所（1:銀座事務所, 2:横浜事務所, 3:鎌倉事務所, 4:津田沼事務所, 5:浜松事務所, 6:その他）');
            $table->string('importantnotes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
