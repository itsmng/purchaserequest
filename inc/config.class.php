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

class PluginPurchaserequestConfig extends CommonDBTM {
   static $rightname         = "plugin_purchaserequest_config";
   var    $can_be_translated = true;

   /**
    * PluginPurchaserequestConfig constructor.
    */
   public function __construct() {

   }

   static function canView() {

      return (Session::haveRight(self::$rightname, READ));
   }

   static function canCreate() {

      return (Session::haveRight(self::$rightname, READ));
   }

   static function getTypeName($nb = 0) {

      return __('Plugin setup', 'purchaserequest');
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {

      return '';
   }

   static function getMenuContent() {

      $menu['title']           = self::getMenuName(2);
      $menu['page']            = self::getSearchURL(false);
      $menu['links']['search'] = self::getSearchURL(false);
      if (self::canCreate()) {
         $menu['links']['add'] = self::getFormURL(false);
      }

      return $menu;
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {


      $item->showForm($item->getID());


      return true;
   }

   function defineTabs($options = []) {

      $ong = [];
      $this->addDefaultFormTab($ong);
      //      $this->addStandardTab(__CLASS__, $ong, $options);

      return $ong;
   }

   function showForm() {
      global $DB, $CFG_GLPI;

      $form = [
        'action' => Toolbox::getItemTypeFormURL(self::getType()),
        'buttons' => [
            'save' => [
                'value' => __('Save'),
                'name' => 'update_config',
                'class' => 'btn btn-secondary',
            ],
        ],
        'content' => [
            __('Configuration purchase request', 'purchaserequest') => [
                'visible' => true,
                'inputs' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id',
                        'value' => $this->fields['id'],
                    ],
                    __('General Services Manager', 'purchaserequest') => [
                        'type' => 'select',
                        'name' => 'id_general_service_manager',
                        'values' => getOptionsForUsers('plugin_purchaserequest_validate'),
                        'value' => $this->fields['id_general_service_manager'],
                        'right' => 'plugin_purchaserequest_validate',
                        'col_lg' => 12,
                        'col_md' => 12,
                    ],
                ],
            ],
        ],
      ];
      renderTwigForm($form);
   }

   /**
    * @param \Migration $migration
    */
   public static function install(Migration $migration) {
      global $DB;

      $dbu   = new DbUtils();
      $table = $dbu->getTableForItemType(__CLASS__);

      if (!$DB->tableExists($table)) {
         $migration->displayMessage("Installing $table");
         $query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_purchaserequest_configs` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `id_general_service_manager` INT(11) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
         $DB->query($query) or die ($DB->error());


         $queryInsert = "INSERT INTO glpi_plugin_purchaserequest_configs VALUES ('1','0')";
         $DB->query($queryInsert) or die ($DB->error());
      } else {

      }

   }

   public static function uninstall() {
      global $DB;

      $dbu   = new DbUtils();
      $table = $dbu->getTableForItemType(__CLASS__);
      $DB->query("DROP TABLE IF EXISTS`" . $table . "`") or die ($DB->error());
   }


}
