<?php

namespace Busybee\Core\SystemBundle\Model;

class MessageManager
{
	/**
	 * @var string
	 */
	private $domain = 'BusybeeHomeBundle';

	/**
	 * @var array
	 */
	private $messages = [];

	/**
	 * Add Message
	 *
	 * @param       $level
	 * @param       $message
	 * @param array $options
	 * @param null  $domain
	 *
	 * @return $this
	 */
	public function addMessage($level, $message, $options = [], $domain = null)
	{
		$mess = new Message();

		$mess->setDomain(is_null($domain) ? $this->getDomain() : $domain);
		$mess->setLevel($level);
		$mess->setMessage($message);
		foreach ($options as $name => $element)
			$mess->addOption($name, $element);

		$this->messages[] = $mess;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDomain(): string
	{
		return $this->domain;
	}

	/**
	 * @param string $domain
	 */
	public function setDomain(string $domain): MessageManager
	{
		$this->domain = $domain;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array
	{
		return $this->messages;
	}
}