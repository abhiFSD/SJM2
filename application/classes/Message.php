<?php

namespace POW\Classes;

class Message
{
	protected $messages = [];

	public function send()
	{
		get_instance()->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode($this->messages));
	}

	public function exec($command, $arguments = null)
	{
		$this->messages[] = [
			'action' => 'exec',
			'command' => $command, 
			'arguments' => $arguments,
		];

		return $this;
	}

	public function html($target, $html)
	{
		$this->messages[] = [
			'action' => 'html',
			'target' => $target,
			'html' => $html,
		];

		return $this;
	}

	public function ok()
	{
		$this->messages[] = [
			'action' => 'ok',
		];
	}

	public function reload()
	{
		$this->messages[] = [
			'action' => 'reload',
		];
	}

	public function redirect($url)
	{
		$this->messages[] = [
			'action' => 'redirect',
			'url' => $url,
		];
	}

	public function close_modal()
	{
		$this->messages[] = [
			'action' => 'close_modal',
		];
	}

}
