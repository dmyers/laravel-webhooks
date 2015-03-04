<?php namespace Webhook;

class Subscriber extends \Eloquent
{
	protected $table = 'webhook_subscribers';
	
	protected $guarded = array('id');
	
	public function subscriptions()
	{
		return $this->hasMany('Webhook\Subscription');
	}
	
	public function notifications()
	{
		return $this->hasMany('Webhook\Notification');
	}
	
	public function subscription($event)
	{
		return $this->subscriptions()
			->where('event', $event)
			->first();
	}
	
	public function notify($event, array $data = [])
	{
		$subscription = Subscription::where('subscriber_id', $this->id)
			->where('event', $event)
			->first();
		
		$data['event'] = $subscription->event;
		$data['timestamp'] = time();
		
		$notification = Notification::create([
			'subscriber_id'   => $this->id,
			'subscription_id' => $subscription->id,
			'data'            => $data,
		]);
		
		return \Queue::push('Dmyers\Webhooks\Notify', ['notification_id' => $notification->id]);
	}
}
