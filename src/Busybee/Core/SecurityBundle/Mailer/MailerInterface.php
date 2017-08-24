<?php

namespace Busybee\Core\SecurityBundle\Mailer;

use Busybee\Core\SecurityBundle\Model\UserInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MailerInterface
{
	/**
	 * Send an email to a user to confirm the account creation
	 *
	 * @param UserInterface $user
	 *
	 * @return void
	 */
	public function sendConfirmationEmailMessage(UserInterface $user);

	/**
	 * Send an email to a user to confirm the password reset
	 *
	 * @param UserInterface $user
	 * @param string        $comment
	 *
	 * @return void
	 */
	public function sendResettingEmailMessage(UserInterface $user, $comment = null);
}
