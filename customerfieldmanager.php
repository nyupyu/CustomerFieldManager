<?php  
if (!defined('_PS_VERSION_')) {  
    exit;  
}  

use Symfony\Component\Form\FormError;  
use Symfony\Component\Validator\Constraints;  
use Symfony\Component\Form\Extension\Core\Type as FormType;  
use Symfony\Component\Form\FormBuilderInterface;  
use Symfony\Component\Form\FormEvent;  
use Symfony\Component\Form\FormEvents;  

class CustomerFieldManager extends Module  
{  
    public function __construct()  
    {  
        $this->name = 'customerfieldmanager';  
        $this->tab = 'administration';  
        $this->version = '1.0.2';  
        $this->author = 'Twoje Imię';  
        $this->need_instance = 0;  

        parent::__construct();  

        $this->displayName = $this->trans('Customer Field Manager', [], 'Modules.Customerfieldmanager.Admin');  
        $this->description = $this->trans('Moduł umożliwiający zarządzanie polami formularza rejestracji klienta.', [], 'Modules.Customerfieldmanager.Admin');  
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];  
    }  

    public function install()  
    {  
        return parent::install() &&  
            $this->registerHook('actionCustomerFormBuilderModifier');  
    }  

    public function hookAdditionalCustomerFormFields($params)
{
    $format = $params['fields'];
    dump($format);
    $format['confirmation_email'] = (new FormField())
    ->setName('confirmation_email')
    ->setType('email')
    ->setLabel($this->trans('Confirm your e-mail address', [], 'Modules.Demooverridecustomerformatter.Front'))
    ->setRequired(true);
    $format['ape_code'] = (new FormField())
    ->setName('ape_code')
    ->setType('ape_code')
    ->setLabel($this->trans('VAT Number', [], 'Modules.Demooverridecustomerformatter.Front'))
    ->setRequired(true);
    unset($format['siret']);
    $params['fields'] = $format;
}

}  