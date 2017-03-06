<?php

namespace Busybee\HomeBundle\Model;


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
     * PersonExtension constructor.
     *
     * @param array $buttons
     */
    public function __construct($buttons, TranslatorInterface $translator)
    {
        $this->buttons = $buttons;
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
        );
    }

    /**
     * @param array $details
     * @return string
     */
    public function saveButton($details = array())
    {
        return $this->generateButton($this->buttons['save'], $details);
    }

    /**
     * @param array $defaults
     * @param array $details
     * @return mixed|string
     */
    private function generateButton($defaults, $details = array())
    {
        $button = '<button title="%title%" type="%type%" class="%class%" style="%style%" %additional%></button>';
        foreach ($defaults as $q => $w) {
            if (!empty($details[$q]))
                $defaults[$q] = $details[$q];
            if ($q == 'title')
                $defaults[$q] = $this->translator->trans($defaults[$q], array(), empty($details['transDomain']) ? 'messages' : $details['transDomain']);
            $button = str_replace('%' . $q . '%', $defaults[$q], $button);
        }
        return $button;
    }

    /**
     * @param array $details
     * @return string
     */
    public function cancelButton($details = array())
    {
        return $this->generateButton($this->buttons['cancel'], $details);
    }

    /**
     * @param array $details
     * @return string
     */
    public function uploadButton($details = array())
    {
        return $this->generateButton($this->buttons['upload'], $details);
    }

    /**
     * @param array $details
     * @return string
     */
    public function addButton($details = array())
    {
        return $this->generateButton($this->buttons['add'], $details);
    }

    /**
     * @param array $details
     * @return string
     */
    public function editButton($details = array())
    {
        return $this->generateButton($this->buttons['edit'], $details);;
    }

    /**
     * @param array $details
     * @return string
     */
    public function proceedButton($details = array())
    {
        return $this->generateButton($this->buttons['proceed'], $details);;
    }
}