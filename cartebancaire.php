<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author olonash <rnasolo@gmail.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class cartebancaire extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public $chequeName;
	public $address;
	public $extra_mail_vars;

	public function __construct()
	{
        $this->name = 'cartebancaire';
        $this->tab = 'payments_gateways';
        $this->version = '1.0';
        $this->author = 'Olonash';
        $this->need_instance = true;
        $this->is_configurable= true;
        $this->ps_versions_compliancy = array('min' => '1.5');

        parent::__construct();

        $this->displayName = $this->l('Carte bancaire');
        $this->description = $this->l('Module payement par carte bancaire.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('CARDBANKPAYMENT'))
            $this->warning = $this->l('No name provided');
	}

    public function install()
    {
        if (Shop::isFeatureActive())
            Shop::setContext(Shop::CONTEXT_ALL);

        return parent::install() &&
        $this->registerHook('payment') &&
        $this->registerHook('paymentReturn') &&
        $this->registerHook('header') &&
        Configuration::updateValue('CARDBANKPAYMENT', 'cartebancaire');
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('CARDBANKPAYMENT')||
            !Configuration::deleteByName('CARTEBANCAIRE_PBX_MODE')||
            !Configuration::deleteByName('CARTEBANCAIRE_PBX_SITE')||
            !Configuration::deleteByName('CARTEBANCAIRE_PBX_RANG')||
            !Configuration::deleteByName('CARTEBANCAIRE_PBX_IDENTFIANT')
            )
            return false;
        return true;
    }

    /**
     * display the cartebancaire form payement in the front
     * @param $params
     *
     * @return mixed
     */
    public function hookPayment($params)
    {
        $this->context->smarty->assign(
            array(
                'my_module_name' => Configuration::get('CARDBANKPAYMENT'),
                'my_module_link' => $this->context->link->getModuleLink('cartebancaire', 'display'),
                'current_customer' =>$this->context->customer,
                'currentcart'=>$this->context->cart,
                'pbx_site' => Configuration::get('CARTEBANCAIRE_PBX_SITE'),
                'pbx_mode' => Configuration::get('CARTEBANCAIRE_PBX_MODE'),
                'pbx_rang' => Configuration::get('CARTEBANCAIRE_PBX_RANG'),
                'pbx_identifiant' => Configuration::get('CARTEBANCAIRE_PBX_IDENTIFIANT')
            )
        );
        return $this->display(__FILE__, 'cartebancaire.tpl');
    }

    /**
     * In the back-end, display module cartebancaire settings
     *
     * @return mixed
     */
    public function getContent(){
        $html = "";
        if(Tools::isSubmit('enregistrer'))        {
            Configuration::updateValue('DATE_RETOUR', Tools::getValue('date_retour'));
            $html .= $this->displayConfirmation($this->l('Vos paramètres ont bien été enregistrés.'));
        }
        return $this->displayForm();
    }


    public function displayForm()
    {
        $html = "";
        if (Tools::isSubmit('submitCarteBancaire'))
        {
            Configuration::updateValue('CARTEBANCAIRE_PBX_MODE', Tools::getValue('pbx_mode'));
            Configuration::updateValue('CARTEBANCAIRE_PBX_SITE', Tools::getValue('pbx_site'));
            Configuration::updateValue('CARTEBANCAIRE_PBX_RANG', Tools::getValue('pbx_rang'));
            Configuration::updateValue('CARTEBANCAIRE_PBX_IDENTIFIANT', Tools::getValue('pbx_identifiant'));
            $html .= $this->displayConfirmation($this->l('Vos paramètres ont bien été enregistrés.'));
        }
        return $html .= $this->renderForm();
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'css/style.css', 'all');
        $this->context->controller->addJS($this->_path.'js/cardbank.js', 'all');
    }

    /**
     * prepare back-end form configuration
     *
     * @return string
     */
    protected function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Mode'),
                        'name' => 'pbx_mode',
                        'values' => array(
                            array(
                                'id' => 'mode_test',
                                'value' => 0,
                                'label' => $this->l('Test')
                            ),
                            array(
                                'id' => 'mode_prod',
                                'value' => 1,
                                'label' => $this->l('Production')
                            ),
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Site'),
                        'name' => 'pbx_site',

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Rang'),
                        'name' => 'pbx_rang',

                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Identifiant'),
                        'name' => 'pbx_identifiant',
                    ),


                ),
                'submit' => array(
                    'title' => $this->l('Enregistrer'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCarteBancaire';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'pbx_mode' => Tools::getValue('pbx_mode', Configuration::get('CARTEBANCAIRE_PBX_MODE')),
            'pbx_site' => Tools::getValue('pbx_site', Configuration::get('CARTEBANCAIRE_PBX_SITE')),
            'pbx_rang' => Tools::getValue('pbx_rang', Configuration::get('CARTEBANCAIRE_PBX_RANG')),
            'pbx_identifiant' => Tools::getValue('pbx_identifiant', Configuration::get('CARTEBANCAIRE_PBX_IDENTIFIANT')),
        );
    }
}
