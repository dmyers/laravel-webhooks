<?php namespace Dmyers\Webhooks;

trait Subscribable
{
	public function subscriptions()
	{
		$subscriber = \Webhook\Subscriber::where('xref_id', $this->id)->first();
		
		return \Webhook\Subscription::where('subscriber_id', $subscriber->id)->first();
	}
	
	public function subscribe($url, $events)
	{
		$subscriber = \Webhook\Subscriber::create(['url' => $url]);
		$subscription = \Webhook\Subscription::subscribe($subscriber, $events);
		
		return $subscriber;
	}
	
	public function unsubscribe($url, $events = null)
	{
		$subscription = \Webhook\Subscription::unsubscribe($subscriber, $events);
		
		if (empty($events)) {
			$subscriber = \Webhook\Subscriber::where('url', $url)->destroy();
		}
	}
}
