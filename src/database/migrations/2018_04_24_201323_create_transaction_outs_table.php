<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_outs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->integer('state_id')->default(2);
            $table->integer('subscription_id');
            $table->decimal('amount');
            $table->text('description');
            $table->date('date')->default(\Carbon\Carbon::now());
            $table->string('origin');
            $table->softDeletes('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_outs');
    }
}
