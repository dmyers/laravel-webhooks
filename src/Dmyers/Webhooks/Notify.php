<?php namespace Dmyers\Webhooks;

class Notify
{
	public function fire($job, $data)
	{
		if ($job->attempts() > \Config::get('laravel-webhooks.max_attempts', 3)) {
			return $job->delete();
		}
		
		$notification_id = array_get($data, 'notification_id');
		
		if (!$notification_id) {
			return $job->delete();
		}
		
		$notification = \Webhook\Notification::find($notification_id);
		
		if (!$notification) {
			return $job->delete();
		}
		
		$webhook_url = $notification->subscriber->url;
		$log = new \Webhook\Log;
		$data = $notification->data;
		$headers = ['Accept' => 'application/json'];
		
		try {
			$response = \GuzzleHttp\post($webhook_url, [
				'headers' => $headers,
				'body'    => $data,
			]);
			$notification->status = 'delivered';
			$log->status = 'success';
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			//$request = $e->getRequest();
			$response = $e->getResponse();
			$notification->status = 'failed';
			$log->status = 'error';
		}
		
		$notification->attempts += 1;
		
		$log->code = $response->getStatusCode();
		$log->response = (string) $response->getBody();
		
		switch ($log->code) {
			case 200:
				$notification->status = 'accepted';
				$log->status = 'success';
				break;
			case 202:
				$notification->status = 'rejected';
				$log->status = 'success';
				break;
			case 403:
				$notification->status = 'rejected';
				$log->status = 'retrying';
				break;
			case 406:
				$notification->status = 'rejected';
				$log->status = 'success';
				break;
			default:
				break;
		}
		
		$log->save();
		$notification->save();
		
		if ($notification->status == 'delivered') {
			$job->delete();
		}
		else {
			sleep(pow(2, $notification->attempts));
		}
	}
}
