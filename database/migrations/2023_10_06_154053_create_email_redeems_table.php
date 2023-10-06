<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_redeems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_id');
            $table->string('email');
            $table->timestamps();

            $table->foreign('code_id')
                ->references('id')
                ->on('codes')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_redeems');
    }
};
