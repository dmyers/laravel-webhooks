<?php

use Illuminate\Database\Migrations\Migration;

class CreateWebhooksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('webhook_subscribers', function ($table) {
			$table->increments('id');
			$table->string('url')->unique();
			$table->string('xref_id')->unique();
			$table->timestamps();
			
			$table->index(array('url'));
		});
		
		Schema::create('webhook_subscriptions', function ($table) {
			$table->increments('id');
			$table->integer('subscriber_id')->unsigned();
			$table->string('event')->unique();
			$table->timestamps();
			
			$table->index(array('subscriber_id', 'event'));
		});
		
		Schema::create('webhook_notifications', function ($table) {
			$table->increments('id');
			$table->integer('subscription_id')->unsigned();
			$table->integer('subscriber_id')->unsigned();
			$table->text('data')->nullable();
			$table->integer('attempts')->unsigned()->default(0);
			$table->string('status');
			$table->timestamps();
			
			$table->index(array('subscription_id', 'subscriber_id'));
		});
		
		Schema::create('webhook_logs', function ($table) {
			$table->increments('id');
			$table->integer('notification_id')->unsigned();
			$table->text('payload')->nullable();
			$table->integer('code');
			//$table->text('request');
			$table->text('response');
			$table->string('status');
			$table->timestamps();
			
			$table->index(array('notification_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('webhook_subscribers');
		Schema::drop('webhook_subscriptions');
		Schema::drop('webhook_notifications');
		Schema::drop('webhook_logs');
	}

}
