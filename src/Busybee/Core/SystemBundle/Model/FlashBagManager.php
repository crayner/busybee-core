<?php

namespace Busybee\Core\SystemBundle\Model;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashBagManager
{
	/**
	 * @var FlashBagInterface
	 */
	private $flashBag;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * FlashBagManager constructor.
	 *
	 * @param FlashBagInterface   $flashBag
	 * @param TranslatorInterface $translator
	 */
	public function __construct(FlashBagInterface $flashBag, TranslatorInterface $translator)
	{
		$this->translator = $translator;
		$this->flashBag   = $flashBag;
	}

	/**
	 * @param array $messages
	 */
	public function addMessages(MessageManager $messages)
	{
		foreach ($messages->getMessages() as $message)
		{
			if (!$message instanceof Message)
				continue;
			$this->flashBag->add($message->getLevel(), $this->translator->trans($message->getMessage(), $message->getOptions(), $message->getDomain()));
		}
	}
}