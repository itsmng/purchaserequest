<?php
/*
 LICENSE

 This file is part of the purchaserequest plugin.

 Purchaserequest plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Purchaserequest plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Purchaserequest. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   purchaserequest
 @author    the purchaserequest plugin team
 @copyright Copyright (c) 2015-2021 Purchaserequest plugin team
 @license   GPLv2+
            http://www.gnu.org/licenses/gpl.txt
 @link      https://github.com/InfotelGLPI/purchaserequest
 @link      http://www.glpi-project.org/
 @since     2009
 ---------------------------------------------------------------------- */

define('PLUGIN_PURCHASEREQUEST_VERSION', '2.1.1');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_purchaserequest() {
   global $PLUGIN_HOOKS, $CFG_GLPI;

   Plugin::registerClass('PluginPurchaserequestProfile');
   $PLUGIN_HOOKS['csrf_compliant']['purchaserequest'] = true;

   /* Init current profile */
   $PLUGIN_HOOKS['change_profile']['purchaserequest'] = ['PluginPurchaserequestProfile', 'initProfile'];

   $plugin = new Plugin();
   if ($plugin->isActivated('purchaserequest')) {

      Plugin::registerClass('PluginPurchaserequestProfile', ['addtabon' => ['Profile']]);

      Plugin::registerClass('PluginPurchaserequestPurchaseRequest', ['addtabon' => ['PluginOrderOrder']]);
      $types = [ComputerType::getType(),
                MonitorType::getType(),
                Peripheraltype::getType(),
                Networkequipmenttype::getType(),
                Printertype::getType(),
                Phonetype::getType(),
                Consumableitemtype::getType(),
                Cartridgeitemtype::getType(),
                Contracttype::getType(),
                Softwarelicensetype::getType(),
                Certificatetype::getType(),
                Racktype::getType(),
                Pdutype::getType(),
                Enclosure::getType()];

      if ($plugin->isActivated('order')) {
         array_push($types, "PluginOrderOtherType");
      }
      Plugin::registerClass(PluginPurchaserequestThreshold::getType(), ['addtabon' => $types]);

      //TODO create right config
      if (Session::haveRight("PluginPurchaserequestConfig", READ)) ;
      {
         $PLUGIN_HOOKS['config_page']['purchaserequest'] = 'front/config.form.php';
      }

      if (PluginPurchaserequestPurchaseRequest::canView()) {
         Plugin::registerClass('PluginPurchaserequestPurchaseRequest',
                               ['notificationtemplates_types' => true,
                                'addtabon'                    => ['Ticket', 'PluginOrderOrder']]);
         $PLUGIN_HOOKS['menu_toadd']['purchaserequest']['management'] = 'PluginPurchaserequestMenu';

         if ($plugin->isActivated('servicecatalog')) {
            $PLUGIN_HOOKS['servicecatalog']['purchaserequest'] = ['PluginPurchaserequestServicecatalog'];
         }
      }

   }
}

/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_purchaserequest() {
   return ['name'         => _n("Purchase request", "Purchase requests", 1, "purchaserequest"),
           'version'      => PLUGIN_PURCHASEREQUEST_VERSION,
           'author'       => "<a href='http://infotel.com/services/expertise-technique/glpi/'>Infotel</a>",
           'license'      => 'GPLv2+',
           'requirements' => [
              'glpi' => [
                 'min' => '9.5',
                 'dev' => false
              ]
           ]];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_purchaserequest_check_prerequisites() {
   global $DB;

   if (version_compare(GLPI_VERSION, '9.5', 'lt')
       || version_compare(GLPI_VERSION, '9.6', 'ge')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '9.5');
      }
      return false;
   }

   $plugin = new Plugin();
   if ($plugin->isActivated("order")
       && !$DB->tableExists("glpi_plugin_order_orders")) {
      return false;
   }
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_purchaserequest_check_config() {
   return true;
}
