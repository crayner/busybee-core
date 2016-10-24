<?php
namespace Busybee\HomeBundle\Model ;

use Symfony\Component\Translation\DataCollectorTranslator as Translator ;
use Symfony\Component\Translation\Loader\YamlFileLoader ;

class MessageManager
{
	private $translator;
	
	private $locale ;
	
	private $domain ;
	
	private $messages ;
	
	public function __construct(Translator $translator, $locale = 'en_GB', $domain = 'BusybeeDisplayBundle')
	{
		$this->translator = $translator ;
		$this->locale = $locale ;
		$this->domain = $domain ;
		$this->translator->addLoader('yaml', new YamlFileLoader()) ;
		$this->translator->addResource('yaml', $this->domain.'.'.$this->locale.'.yml', $this->domain ) ;
		$this->messages = '';
	}
	/**
	 * message
	 *
	 * @param	string	Message
	 * @param	string	style
	 */
	public function message($message, $style = null, $domain = null)
	{
		if ($domain !== null && $domain !== $this->domain)
			$this->translator->addResource('yaml', $domain.'.'.$this->locale.'.yml', $domain ) ;
		else
			$domain = $this->domain ;
		$style = strtolower($style);
		switch ($style) 
		{
			case 'success':
				return $this->addStyle($this->translator->trans($message, array(), $domain), $style);
			case 'danger':
				return $this->addStyle($this->translator->trans($message, array(), $domain), $style);
			case 'warning':
				return $this->addStyle($this->translator->trans($message, array(), $domain), $style);
			case 'info':
				return $this->addStyle($this->translator->trans($message, array(), $domain), $style);
			default:
				return $this->translator->trans($message, array(), $domain);
		}
	}

	private function addStyle($message, $style)
	{				
		return $this->messages .= "
					<div class=\"row\">
						<div class=\"col-sm-12\">
							<div class=\"alert alert-".$style."\">
								".$message."
							</div>
						</div>
					</div>
";
	}
	
	public function getMessages()
	{
		return $this->messages;	
	}
}