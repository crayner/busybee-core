<?php
/**
 * FormErrorsExtension - extension to list all errors from form.
 *
 * @author Maciej Szkamruk <ex3v@ex3v.com>
 */
namespace Busybee\FormBundle\Model ;

use Busybee\FormBundle\Model\FormErrorsParser;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\DataCollectorTranslator as Translator;


class FormErrorsExtension extends \Twig_Extension
{
    /**
     * @var FormErrorsParser
     */
    private $parser;
    /**
     * @var Translator
     */
    private $trans ;
	
    public function __construct(FormErrorsParser $parser, $trans)
    {
        $this->parser = $parser;
		if ($trans instanceof Translator || $trans instanceof TranslatorInterface)
			$this->trans = $trans ;
		else
			throw new \Exception('Invalid Translator Supplied.');
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('all_form_errors', array($this, 'getFormErrors'), array('is_safe' => array('html'))),
        );
    }
    /**
     * Main Twig extension. Call this in Twig to get formatted output of your form errors.
     * Note that you have to provide form as Form object, not FormView.
     * 
     * @param   FormInterface  $form
     * @param   string         $tag    The html tag, in which all errors will be packed. If you provide 'li',
     *                                 'ul' wrapper will be added
     * @param   string         $class  Class of each error. Default is none.
     * @return  string
     */
    public function getFormErrors(FormInterface $form, $tag = 'li', $class = '', $noErrorMessage = '', $noErrorClass = '')
    {
		if (! $form->isSubmitted()) return '';
        $errorsList = $this->parser->parseErrors($form);
        $return = '';
        if (count($errorsList) > 0) {
            if ($tag == 'li') {
                $return .= '<ul>';
            }
            foreach ($errorsList as $item) {
                $return .= $this->handleErrors($item, $tag, $class);
            }
            if ($tag == 'li') {
                $return .= '</ul>';
            }
        }
		if (empty($return) && ! empty($noErrorMessage))
		{
            if ($tag == 'li') {
                $return .= '<ul>';
            }
			$return .= '<'. $tag .' class="'. $noErrorClass .'">';
			$return .= $noErrorMessage;
			$return .= '</'. $tag . '>';
			
            if ($tag == 'li') {
                $return .= '</ul>';
            }
		}
        return $return;
    }
    /**
     * Handle single error creation.
     *
     * @param   array   $item
     * @param   string  $tag
     * @param   string  $class
     * @return  string
     */
    private function handleErrors($item, $tag, $class)
    {
        $return = '';
        $errors = $item['errors'];
        if (count($errors) > 0) {
            /** @var FormError $error */
            foreach ($errors as $error) {
                $return .= '<'. $tag .' class="'. $class .'">';
                $return .= $this->trans->trans($item['label'], array(), $item['translation']);
                $return .= ': ';
                $return .= $error->getMessage();  // The translator has already translated any validation error.
                $return .= '</'. $tag . '>';
            }
        }
        return $return;
    }

    public function getName()
    {
        return 'all_form_errors_extension';
    }
}