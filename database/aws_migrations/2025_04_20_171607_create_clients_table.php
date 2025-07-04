<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // クライアントID（主キー）

            $table->tinyInteger('client_type');
            $table->string('name_kanji', 255);
            $table->string('name_kana', 255);
            $table->string('name_abbreviation', 255)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('phone_number2', 15)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('home_phone_number', 15)->nullable();
            $table->tinyInteger('not_home_contact')->nullable();
            $table->string('first_contact_number', 15)->nullable();
            $table->string('second_contact_number', 15)->nullable();
            $table->string('fax', 15)->nullable();
            $table->string('email1', 100)->nullable();
            $table->string('email2', 100)->nullable();

            //住所(address)
            $table->string('address_postalcode', 10)->nullable();
            $table->string('address_state', 10)->nullable();
            $table->string('address_city', 20)->nullable();
            $table->string('address_street', 100)->nullable();
            $table->string('address_name_kanji', 255)->nullable();
            $table->string('address_name_kana', 255)->nullable();

            // 配送先住所(contact)
            $table->string('contact_postalcode', 10)->nullable();
            $table->string('contact_state', 10)->nullable();
            $table->string('contact_city', 20)->nullable();
            $table->string('contact_street', 100)->nullable();
            $table->string('contact_name_kanji', 255)->nullable();
            $table->string('contact_name_kana', 255)->nullable();

            $table->string('contact_address_notes', 1000)->nullable();

            // 名前（氏名）・ふりがな　個人用
            $table->string('last_name_kanji', 100)->nullable();
            $table->string('first_name_kanji', 155)->nullable();
            $table->string('last_name_kana', 100)->nullable();
            $table->string('first_name_kana', 155)->nullable();

            // 本人確認書類　個人用
            $table->tinyInteger('identification_document1')->nullable();
            $table->tinyInteger('identification_document2')->nullable();
            $table->tinyInteger('identification_document3')->nullable();

            // 各種送付フラグ
            $table->tinyInteger('send_newyearscard')->nullable();
            $table->tinyInteger('send_summergreetingcard')->nullable();
            $table->tinyInteger('send_office_news')->nullable();
            $table->tinyInteger('send_autocreation')->nullable();

            // 取引先責任者　法人用
            $table->string('contact_last_name_kanji', 100)->nullable();
            $table->string('contact_first_name_kanji', 155)->nullable();
            $table->string('contact_last_name_kana', 100)->nullable();
            $table->string('contact_first_name_kana', 155)->nullable();
            $table->string('contact_phone_number', 15)->nullable();
            $table->string('contact_mobile_number', 15)->nullable();
            $table->string('contact_home_phone_number', 15)->nullable();
            $table->string('contact_email1', 100)->nullable();
            $table->string('contact_email2', 100)->nullable();
            $table->string('contact_fax', 15)->nullable();

            $table->timestamps(); // created_at / updated_at


            // インデックス見直し20250702
            $table->index('client_type');
            $table->index('name_kanji');
            $table->index('name_kana');
            $table->index('phone_number');
            $table->index('phone_number2');
            $table->index('mobile_number');
            $table->index('home_phone_number');
            $table->index('first_contact_number');
            $table->index('second_contact_number');
            $table->index('email1');
            $table->index('email2');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
