<?php
/**
* 2007-2017 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfirmationEmailAttachment extends Module
{
    protected $config_form = false;

    protected $target_directory;

    public function __construct()
    {
        $this->name = 'confirmationemailattachment';
        $this->tab = 'emailing';
        $this->version = '0.1.0';
        $this->author = 'Traxlead';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Confirmation Email Attachment');
        $this->description = $this->l('Allows administrator to attach a document in the Order Confirmation Email.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.0.6');

        // Upload directory
        $this->target_directory = _PS_ROOT_DIR_ . '/upload/';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('CEA_ENABLED');
        Configuration::deleteByName('CEA_ATTACHMENT_FILE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitConfirmationEmailAttachmentModule')) == true) {
            $this->postProcess();
        }

        $attachment_path = str_replace(_PS_ROOT_DIR_, '', Configuration::get('CEA_ATTACHMENT_FILE'));
        $attachment_name = str_replace($this->target_directory, '', Configuration::get('CEA_ATTACHMENT_FILE'));


        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('attachment_path', $attachment_path);
        $this->context->smarty->assign('attachment_name', $attachment_name);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitConfirmationEmailAttachmentModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enabled'),
                        'name' => 'CEA_ENABLED',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 8,
                        'type' => 'file',
                        'desc' => $this->l('Chose a file to attach with your order confirmation email'),
                        'name' => 'CEA_ATTACHMENT_FILE',
                        'label' => $this->l('Attachment'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CEA_ENABLED'         => Configuration::get('CEA_ENABLED'),
            'CEA_ATTACHMENT_FILE' => Configuration::get('CEA_ATTACHMENT_FILE')
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        // $form_values = $this->getConfigFormValues();

        $target_file =  $this->target_directory . $_FILES['CEA_ATTACHMENT_FILE']['name'];

        move_uploaded_file($_FILES['CEA_ATTACHMENT_FILE']['tmp_name'], $target_file);

        if(!empty($_FILES['CEA_ATTACHMENT_FILE']['tmp_name']))
            Configuration::updateValue('CEA_ATTACHMENT_FILE', $target_file);
        
        Configuration::updateValue('CEA_ENABLED', Tools::getValue('CEA_ENABLED'));
    }
}
