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
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');

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
            !Configuration::deleteByName('CARDBANKPAYMENT'))
            return false;
        return true;
    }

    public function hookPayment($params)
    {
        /*if (!$this->active)
            return;
        if (!$this->checkCurrency($params['cart']))
            return;*/
//print_r($this->context->cart); die;

        $this->context->smarty->assign(
            array(
                'my_module_name' => Configuration::get('CARDBANKPAYMENT'),
                'my_module_link' => $this->context->link->getModuleLink('cartebancaire', 'display'),
                'current_customer' =>$this->context->customer,
                'currentcart'=>$this->context->cart
            )
        );
        return $this->display(__FILE__, 'cartebancaire.tpl');
    }



    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'css/style.css', 'all');
        $this->context->controller->addJS($this->_path.'js/cardbank.js', 'all');
    }
}
