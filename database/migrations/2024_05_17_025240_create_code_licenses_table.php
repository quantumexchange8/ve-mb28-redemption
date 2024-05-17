<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('code_licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('code_id');
            $table->unsignedBigInteger('setting_license_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('code_id')
                ->references('id')
                ->on('codes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('setting_license_id')
                ->references('id')
                ->on('setting_licenses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_licenses');
    }
};
