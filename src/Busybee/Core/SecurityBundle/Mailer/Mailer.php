<?php

namespace Busybee\Core\SecurityBundle\Mailer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Busybee\Core\SecurityBundle\Model\UserInterface;
use Busybee\Core\SecurityBundle\Mailer\MailerInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer implements MailerInterface
{
	protected $mailer;
	protected $router;
	protected $templating;
	protected $parameters;

	public function __construct($mailer, UrlGeneratorInterface $router, EngineInterface $templating, array $parameters)
	{
		$this->mailer     = $mailer;
		$this->router     = $router;
		$this->templating = $templating;
		$this->parameters = $parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function sendConfirmationEmailMessage(UserInterface $user)
	{
		$template = $this->parameters['confirmation.template'];
		$url      = $this->router->generate('busybee_security_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);
		$rendered = $this->templating->render($template, array(
			'user'            => $user,
			'confirmationUrl' => $url
		));
		$this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], $user->getEmail());
	}

	/**
	 * @param string $renderedTemplate
	 * @param string $fromEmail
	 * @param string $toEmail
	 */
	protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
	{
		// Render the email, use the first line as the subject, and the rest as the body
		$renderedLines = explode("\n", trim($renderedTemplate));
		$subject       = $renderedLines[0];
		$body          = implode("\n", array_slice($renderedLines, 1));

		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($fromEmail)
			->setTo($toEmail)
			->setBody($body);

		$this->mailer->send($message);
	}

	/**
	 * {@inheritdoc}
	 */
	public function sendResettingEmailMessage(UserInterface $user, $comment = null)
	{
		$template = $this->parameters['resetting.template'];
		$url      = $this->router->getContext()->getScheme() . '://' . $this->router->getContext()->getHost() . $this->router->generate('security_user_reset_reset', array('token' => $user->getConfirmationToken()), true);


		$rendered = $this->templating->render($template, array(
			'user'            => $user,
			'comment'         => $comment,
			'confirmationUrl' => $url
		));
		$this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail());
	}
}
