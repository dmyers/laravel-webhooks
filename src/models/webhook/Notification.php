<?php namespace Webhook;

class Notification extends \Eloquent
{
	protected $table = 'webhook_notifications';
	
	protected $guarded = array('id');
	
	public function subscriber()
	{
		return $this->belongsTo('Webhook\Subscriber');
	}
	
	public function subscription()
	{
		return $this->belongsTo('Webhook\Subscription');
	}
	
	public function getDataAttribute($value)
	{
		return json_decode($value, true);
	}
	
	public function setDataAttribute($value)
	{
		$this->attributes['data'] = json_encode($value);
	}
}