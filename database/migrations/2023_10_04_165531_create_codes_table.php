<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->string('redemption_code');
            $table->string('acc_name')->nullable();
            $table->string('broker_name')->nullable();
            $table->string('license_name')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('status')->default('valid');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('codes');
    }
};
