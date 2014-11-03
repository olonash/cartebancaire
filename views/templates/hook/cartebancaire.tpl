{*
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
*  @author Olonash <rnasolo@gmail.com.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="col-xs-12 col-md-6">
    <p class="payment_module">
        <a title="Payer par carte bancaire" href="#" class="cartebancaire">
            <img src="modules/cartebancaire/img/cb.png" width="15%" alt=""> Payer par carte bancaire
        </a>
    </p>
    <form id="paiement_etransanction" action = "{$link->getModuleLink('cartebancaire', 'cgi-bin/modulev500-RhE5-64bits.cgi')|escape:'html'}" METHOD = "post">
    {*<form id="paiement_etransanction" action = "https://tpeweb.paybox.com/cgi/MYpagepaiement.cgi" METHOD = "post">*}
        <input type = "hidden" name = PBX_MODE value = '{$pbx_mode}'> <!-- passage par formulaire --><br>
        <input  type = "hidden" name = PBX_SITE value = '{$pbx_site}'> <br>
        <input  type = "hidden" name = PBX_RANG value = '{$pbx_rang}'> <br>
        <input  type = "hidden" name = PBX_IDENTIFIANT value = '{$pbx_identifiant}'><br>
        <input  type = "hidden" name = PBX_TOTAL value = '{$total_price*100}'> <br>
        <input  type = "hidden" name = PBX_DEVISE value = '978'> <br>
        <input  type = "hidden" name = PBX_CMD value = '{$currentcart->id}'> <br>
        <input  type = "hidden" name = PBX_PORTEUR value = "{$current_customer->email}"> <br>
        <input  type = "hidden" name = PBX_RETOUR value = 'montant:M;ref:R;auto:A;trans:T'> <br>
        <input  type = "hidden" name = PBX_EFFECTUE value = "{$link->getModuleLink('cartebancaire', 'paymentreturn', ['status'=>'ok'])|escape:'html'}"> <br>
        <input  type = "hidden" name = PBX_REFUSE value = "{$link->getModuleLink('cartebancaire', 'paymentreturn', ['status'=>'refused'])|escape:'html'}"> <br>
        <input  type = "hidden" name = PBX_ANNULE value = "{$link->getModuleLink('cartebancaire', 'paymentreturn', ['status'=>'cancelled'])|escape:'html'}"> <br>
        <input  type = "hidden" name = PBX_TXT value = ' '><br>
        <input  type = "hidden" name = PBX_WAIT value = '0'><br>
        <input  type = "hidden" name = PBX_BOUTPI value = 'nul'><br>
        <input  type = "hidden" name = PBX_BKGD value = 'white'><br>
        <input  type = "hidden" name = PBX_LANGUE value = 'FRA'><br>
        <input  type = "hidden" name = PBX_ERREUR value = {$link->getModuleLink('cartebancaire', 'paymentreturn', ['status'=>'error'])|escape:'html'}><br>
        <input  type = "hidden" name = PBX_TYPECARTE value = 'CB'><br>
    </form>
</div>