<?php

namespace Busybee\Core\TemplateBundle\Extension;


use Busybee\Core\HomeBundle\Exception\Exception;
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
			new \Twig_SimpleFunction('onButton', array($this, 'onButton')),
			new \Twig_SimpleFunction('offButton', array($this, 'offButton')),
			new \Twig_SimpleFunction('onOffButton', array($this, 'onOffButton')),
			new \Twig_SimpleFunction('upDownButton', array($this, 'upDownButton')),
			new \Twig_SimpleFunction('toggleButton', array($this, 'toggleButton')),
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

		if (isset($details['additional']) && is_array($details['additional']))
		{
			$additional            = $details['additional'];
			$details['additional'] = '';
			foreach ($additional as $name => $value)
				$details['additional'] = $name . '="' . $value . '" ';
			$details['additional'] = trim($details['additional']);
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

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function toggleButton(array $details)
	{
		$toggle = '<div class="divClass"><input type="checkbox" attributes inputClass></div>';

		$details['class'] = empty($details['class']) ? 'toggle form-control' : $details['class'] . ' toggle form-control';
		$vars             = $details['form']->vars;

		$toggle = str_replace('divClass', $vars['div_class'], $toggle);

		$attributes = [];

		$attributes['data-toggle'] = 'toggle';

		$attributes['data-off'] = empty($vars['attr']['data-off']) ? '<span class=\'halflings halflings-thumbs-down\'></span>' : $vars['attr']['data-off'];

		$attributes['data-on'] = empty($vars['attr']['data-on']) ? '<span class=\'halflings halflings-thumbs-up\'></span>' : $vars['attr']['data-on'];

		$attributes['data-size'] = empty($vars['attr']['data-size']) ? 'small' : $vars['attr']['data-size'];

		$attributes['data-onstyle'] = empty($vars['attr']['data-onstyle']) ? 'success' : $vars['attr']['data-onstyle'];

		$attributes['data-offstyle'] = empty($vars['attr']['data-offstyle']) ? 'danger' : $vars['attr']['data-offstyle'];

		$attributes['data-height'] = empty($vars['attr']['data-height']) ? '' : $vars['attr']['data-height'];

		$attributes['data-width'] = empty($vars['attr']['data-width']) ? '' : $vars['attr']['data-width'];

		$attributes['name'] = $vars['full_name'];

		$attributes['id'] = $vars['id'];

		if (isset($attributes['value']))
			$attributes['value'] = $vars['value'];

		if ($vars['checked'])
			$attributes['checked'] = 'checked';

		$attributes['style'] = empty($vars['attr']['style']) ? 'float: right;' : $vars['attr']['style'];

		$attrib = '';
		foreach ($attributes as $name => $value)
		{
			$attrib .= ' ' . $name . '="' . $value . '"';
			$attrib = trim($attrib);
		}

		$vars['attr']['class'] = empty($vars['attr']['class']) ? '' : 'class="' . $vars['attr']['class'] . '"';
		$toggle                = str_replace('attributes', $attrib . ' data-height=20 data-width=40', $toggle);
		$toggle                = str_replace('inputClass', $vars['attr']['class'], $toggle);

		return $toggle;
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function onButton($details = [])
	{
		return $this->generateButton($this->buttons['on'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function offButton($details = [])
	{
		return $this->generateButton($this->buttons['off'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function onOffButton($details = [])
	{
		if (!isset($details['value']))
			throw new Exception('You must set a boolean value for the On/Off Button.');
		if ($details['value'])
			return $this->generateButton($this->buttons['on'], isset($details['on']) ? $details['on'] : []);
		else
			return $this->generateButton($this->buttons['off'], isset($details['off']) ? $details['off'] : []);
	}
}