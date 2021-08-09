<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckOutRequestIdColumnToMpesaTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->string('CheckoutRequestID')->nullable()->unique();
            $table->unsignedBigInteger('profile_id')->nullable();
            // $table->string('customer_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mpesa_transactions', function (Blueprint $table) {
            $table->dropColumn(['CheckoutRequestID', 'profile_id']);
        });
    }
}
