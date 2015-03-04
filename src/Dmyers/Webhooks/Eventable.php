<?php namespace Dmyers\Webhooks;

trait Eventable
{
	public static function bootEventable()
	{
		$events = static::$eventable;
		
		foreach ($events as $map => $event) {
			static::registerModelEvent($event, function($model) use ($event) {
				$model->publish($event);
			});
		}
	}
	
	public function publish($event, array $data = [])
	{
		$sync_data = [];
		
		foreach ($this->eventable_sync as $property) {
			$sync_data[$property] = $this->getAttribute($property);
		}
		
		\Webhook\Subscription::publish($event, array_merge($data, $sync_data));
	}
}
