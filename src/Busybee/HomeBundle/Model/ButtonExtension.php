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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('saveButton', array($this, 'saveButton')),
            new \Twig_SimpleFunction('cancelButton', array($this, 'cancelButton')),
            new \Twig_SimpleFunction('uploadButton', array($this, 'uploadButton')),
        );
    }

    /**
     * @param array $details
     * @return string
     */
    public function saveButton($details = array())
    {
        $button = '<button title="%title%" type="%type%" class="%class%" style="%style%" %additional%></button>';
        $defaults = $this->buttons['save'];
        foreach ($defaults as $q => $w) {
            if (!empty($details[$q]))
                $defaults[$q] = $details[$q];
            if ($q == 'title')
                $defaults[$q] = $this->translator->trans($defaults[$q]);
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
        $button = '<button title="%title%" type="%type%" class="%class%" style="%style%" %additional%></button>';
        $defaults = $this->buttons['cancel'];
        foreach ($defaults as $q => $w) {
            if (!empty($details[$q]))
                $defaults[$q] = $details[$q];
            if ($q == 'title')
                $defaults[$q] = $this->translator->trans($defaults[$q]);
            $button = str_replace('%' . $q . '%', $defaults[$q], $button);
        }
        return $button;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'button_twig_extension';
    }

    /**
     * @param array $details
     * @return string
     */
    public function uploadButton($details = array())
    {
        $button = '<button title="%title%" type="%type%" class="%class%" style="%style%" %additional%></button>';
        $defaults = $this->buttons['upload'];
        foreach ($defaults as $q => $w) {
            if (!empty($details[$q]))
                $defaults[$q] = $details[$q];
            if ($q == 'title')
                $defaults[$q] = $this->translator->trans($defaults[$q]);
            $button = str_replace('%' . $q . '%', $defaults[$q], $button);
        }
        return $button;
    }
}