<?php

namespace Busybee\Core\HomeBundle\Model;


use Symfony\Component\Translation\TranslatorInterface;

class ButtonExtension extends \Twig_Extension
{
	/**
	 * @var array
	 */
	private $buttons;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * ButtonExtension constructor.
	 *
	 * @param                     $buttons
	 * @param TranslatorInterface $translator
	 */
	public function __construct($buttons, TranslatorInterface $translator)
	{
		$this->buttons    = $buttons;
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'button_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('saveButton', array($this, 'saveButton')),
			new \Twig_SimpleFunction('cancelButton', array($this, 'cancelButton')),
			new \Twig_SimpleFunction('uploadButton', array($this, 'uploadButton')),
			new \Twig_SimpleFunction('addButton', array($this, 'addButton')),
			new \Twig_SimpleFunction('editButton', array($this, 'editButton')),
			new \Twig_SimpleFunction('proceedButton', array($this, 'proceedButton')),
			new \Twig_SimpleFunction('returnButton', array($this, 'returnButton')),
			new \Twig_SimpleFunction('deleteButton', array($this, 'deleteButton')),
			new \Twig_SimpleFunction('miscButton', array($this, 'miscButton')),
			new \Twig_SimpleFunction('resetButton', array($this, 'resetButton')),
			new \Twig_SimpleFunction('closeButton', array($this, 'closeButton')),
			new \Twig_SimpleFunction('upButton', array($this, 'upButton')),
			new \Twig_SimpleFunction('downButton', array($this, 'downButton')),
			new \Twig_SimpleFunction('upDownButton', array($this, 'upDownButton')),
		);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function saveButton($details = array())
	{
		return $this->generateButton($this->buttons['save'], $details);
	}

	/**
	 * @param array $defaults
	 * @param array $details
	 *
	 * @return mixed|string
	 */
	private function generateButton($defaults, $details = array())
	{
		$button = '<button title="%title%" type="%type%" class="%class%" style="%style%" %additional%>%prompt%</button>';

		if (isset($details['mergeClass']))
		{
			if (isset($defaults['class']))
				$defaults['class'] .= ' ' . $details['mergeClass'];
		}

		if (!empty($details['windowOpen']))
		{
			$target                = empty($details['windowOpen']['target']) ? '_self' : $this->translator->trans($details['windowOpen']['target'], array(), empty($details['transDomain']) ? 'messages' : $details['transDomain']);
			$route                 = 'onClick="window.open(\'' . $details['windowOpen']['route'] . '\',\'' . $target . '\'';
			$route                 = empty($details['windowOpen']['params']) ? $route . ')"' : $route . ',\'' . $details['windowOpen']['params'] . '\')"';
			$details['additional'] = empty($details['additional']) ? $route : trim($details['additional'] . ' ' . $route);
		}

		if (!empty($details['javascript']))
		{
			$target = '';
			if (!empty($details['javascript']['options']))
			{
				foreach ($details['javascript']['options'] as $option)
					$target .= '\'' . $option . '\',';
			}
			$target = trim($target, ',');

			$route                 = 'onClick="' . $details['javascript']['function'] . '(' . $target . ');"';
			$details['additional'] = empty($details['additional']) ? $route : trim($details['additional'] . ' ' . $route);
		}

		foreach ($defaults as $q => $w)
		{
			if (isset($details[$q]))
				$defaults[$q] = $details[$q];
			if (empty($defaults[$q]))
			{
				unset($defaults[$q]);
				$button = str_replace(array($q . '="%' . $q . '%"', '%' . $q . '%'), '', $button);
			}
			else
			{
				if (in_array($q, ['title', 'prompt']))
					if (is_array($defaults[$q]))
						$defaults[$q] = $this->translator->trans($defaults[$q]['message'], $defaults[$q]['params'], empty($details['transDomain']) ? 'messages' : $details['transDomain']);
					else
						$defaults[$q] = $this->translator->trans($defaults[$q], [], empty($details['transDomain']) ? 'messages' : $details['transDomain']);
				$button = str_replace('%' . $q . '%', $defaults[$q], $button);
			}
		}

		if (isset($details['collectionName']))
			$button = str_replace('collection', $details['collectionName'], $button);

		if (isset($details['colour']))
			$button = str_replace(['btn-default', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-primary', 'btn-link'], 'btn-' . $details['colour'], $button);

		return $button;
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function cancelButton($details = array())
	{
		return $this->generateButton($this->buttons['cancel'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function uploadButton($details = array())
	{
		return $this->generateButton($this->buttons['upload'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function addButton($details = array())
	{
		return $this->generateButton($this->buttons['add'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function editButton($details = array())
	{
		return $this->generateButton($this->buttons['edit'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function proceedButton($details = array())
	{
		return $this->generateButton($this->buttons['proceed'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function returnButton($details = array())
	{
		return $this->generateButton($this->buttons['return'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function deleteButton($details = array())
	{
		return $this->generateButton($this->buttons['delete'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function miscButton($details = array())
	{
		return $this->generateButton($this->buttons['misc'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function resetButton($details = array())
	{
		return $this->generateButton($this->buttons['reset'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function closeButton($details = array())
	{
		return $this->generateButton($this->buttons['close'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function upButton($details = array())
	{
		return $this->generateButton($this->buttons['up'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function downButton($details = array())
	{
		return $this->generateButton($this->buttons['down'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function upDownButton($details = array())
	{
		return $this->generateButton($this->buttons['down'], $details) . $this->generateButton($this->buttons['up'], $details);
	}
}