<?php namespace Webhook;

class Log extends \Eloquent
{
	protected $table = 'webhook_logs';
	
	protected $guarded = array('id');
}
