<?php  
if (!defined('_PS_VERSION_')) {  
    exit;  
}  

use Symfony\Component\Form\FormError;  
use Symfony\Component\Validator\Constraints as Assert;  
use Symfony\Component\Form\Extension\Core\Type as FormType;  
use Symfony\Component\Form\FormBuilderInterface;  
use Symfony\Component\Form\FormEvent;  
use Symfony\Component\Form\FormEvents;  
use PrestaShopBundle\Form\Admin\Type\SwitchType;  

class CustomerFieldManager extends Module  
{  
    public function __construct()  
    {  
        $this->name = 'customerfieldmanager';  
        $this->tab = 'administration';  
        $this->version = '1.0.2';  
        $this->author = 'Oktawian Wybieralski';  
        $this->need_instance = 0;  

        parent::__construct();  

        $this->displayName = $this->trans('Customer Field Manager', [], 'Modules.Customerfieldmanager.Admin');  
        $this->description = $this->trans('Moduł umożliwiający zarządzanie polami formularza rejestracji klienta.', [], 'Modules.Customerfieldmanager.Admin');  
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];  
    }  

    public function install()  
    {  
        return parent::install() &&  
            $this->registerHook('additionalCustomerFormFields') &&  
            // $this->registerHook('actionCustomerFormBuilderModifier') &&  
            $this->registerHook('actionAfterUpdateCustomerFormHandler');  
    }  

    public function uninstall()  
    {  
        return parent::uninstall() &&  
            $this->unregisterHook('additionalCustomerFormFields') &&  
            // $this->unregisterHook('actionCustomerFormBuilderModifier') &&  
            $this->unregisterHook('actionAfterUpdateCustomerFormHandler');  
    }  

    public function hookAdditionalCustomerFormFields($params)  
    {  
        $fields = $params['fields'];  

        $customerId = null;  
        if (isset($params['object']) && $params['object'] !== null) {  
            $customerId = $params['object']->id;  
        }  

        $fields['ape'] = (new FormField())  
        ->setName('ape')  
        ->setType('text')  
        ->setLabel($this->trans('Tax Identification Number (NIP)', [], 'Modules.Customerfieldmanager.Front'))  
        ->setRequired(false)  
        ->setValue($customerId ? $this->getCustomerApe($customerId) : '');
        // ->addConstraint('checkApeFormat'); 

        $params['fields'] = $fields;  
    }  

    // public function checkApeFormat($value) {  
    //     return preg_match('/^\d{10}$/', $value); // Example logic  
    // }  

    // public function hookActionCustomerFormBuilderModifier(array $params)  
    // {  
    //     $formBuilder = $params['form_builder'];  
    //     $formBuilder->add('ape', FormType\TextType::class, [  
    //         'label' => $this->getTranslator()->trans('Tax Identification Number (NIP)', [], 'Modules.Customerfieldmanager.Back'),  
    //         'required' => false,  
    //         'constraints' => [  
    //             new Assert\Regex([  
    //                 'pattern' => '/^\d{10}$/',  
    //                 'message' => $this->trans('Invalid NIP format. The NIP must consist of 10 digits.', [], 'Modules.Customerfieldmanager.Back'),  
    //             ]),  
    //         ],  
    //     ]);  

    //     $customerId = $params['id'];  

    //     if ($params['data'] && isset($params['data']['ape'])) {  
    //         $params['data']['ape'] = $this->getCustomerApe($customerId);  
    //     }  

    //     $formBuilder->setData($params['data']);  
    // }  

    private function getCustomerApe($customerId)  
    {  
        $sql = new DbQuery();  
        $sql->select('ape');  
        $sql->from('customer');  
        $sql->where('id_customer = ' . (int)$customerId);  

        return Db::getInstance()->getValue($sql);  
    }  

    public function saveCustomerApe($customerId, $ape)  
    {  
        $ape = pSQL($ape);  

        Db::getInstance()->update('customer', ['ape' => $ape], 'id_customer = ' . (int)$customerId);  
    }  

    public function hookActionAfterUpdateCustomerFormHandler($params)  
    {  
        $customer = $params['customer'];  
        if ($customer->id && isset($params['form_data']['ape'])) {  
            $this->saveCustomerApe($customer->id, $params['form_data']['ape']);  
        }  
    }  
}