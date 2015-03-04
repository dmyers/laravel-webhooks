<?php namespace Webhook;

class Subscription extends \Eloquent
{
	protected $table = 'webhook_subscriptions';
	
	protected $guarded = array('id');
	
	public function subscriber()
	{
		return $this->belongsTo('Webhook\Subscriber');
	}
	
	public static function subscribers($event)
	{
		return static::with('subscriber')
			->where('event', $event)
			->get();
	}
	
	public static function subscribe($subscriber, $events)
	{
		$events = (array) $events;
		
		$exists = static::where('subscriber_id', $subscriber->id)
			->whereIn($events)
			->get()
			->list('event');
		
		foreach ($event as $e) {
			if (in_array($e, $exists)) continue;
			
			$event = static::create([
				'subscriber_id' => $subscriber->id,
				'event'         => $e,
			]);
		}
	}
	
	public static function unsubscribe($subscriber, $events)
	{
		if (empty($events)) {
			static::where('event', $event)->destroy();
		}
		else {
			$events = (array) $events;
			
			foreach ($event as $e) {
				$event = static::where('subscriber_id', $subscriber->id)
					->where($e)
					->first();
				
				if ($event) continue;
				
				$event = static::create([
					'subscriber_id' => $subscriber->id,
					'event'         => $e,
				]);
			}
		}
	}
	
	public static function publish($event, $data)
	{
		$subscriptions = static::subscribers($event);
		
		foreach ($subscriptions as $subscription) {
			$subscription->subscriber->notify($event, $data);
		}
	}
}
