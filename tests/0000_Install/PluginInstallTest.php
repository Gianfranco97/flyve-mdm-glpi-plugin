<?php
/**
 * LICENSE
 *
 * Copyright © 2016-2017 Teclib'
 * Copyright © 2010-2016 by the FusionInventory Development Team.
 *
 * This file is part of Flyve MDM Plugin for GLPI.
 *
 * Flyve MDM Plugin for GLPI is a subproject of Flyve MDM. Flyve MDM is a mobile
 * device management software.
 *
 * Flyve MDM Plugin for GLPI is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Flyve MDM Plugin for GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License
 * along with Flyve MDM Plugin for GLPI. If not, see http://www.gnu.org/licenses/.
 * ------------------------------------------------------------------------------
 * @author    Thierry Bugier Pineau
 * @copyright Copyright © 2017 Teclib
 * @license   AGPLv3+ http://www.gnu.org/licenses/agpl.txt
 * @link      https://github.com/flyve-mdm/flyve-mdm-glpi
 * @link      https://flyve-mdm.com/
 * ------------------------------------------------------------------------------
 */

use Glpi\Test\CommonTestCase;
use Glpi\Test\PluginDB;

class PluginInstallTest extends CommonTestCase
{

   public function setUp() {
      parent::setUp();
      self::setupGLPIFramework();
      self::login('glpi', 'glpi', true);
   }

   protected function setupGLPI() {
      global $CFG_GLPI;

      $settings = [
            'use_notifications' => '1',
            'notifications_mailing' => '1',
            'enable_api'  => '1',
            'enable_api_login_credentials'  => '1',
            'enable_api_login_external_token'  => '1',
      ];
      Config::setConfigurationValues('core', $settings);

      $CFG_GLPI = $settings + $CFG_GLPI;
   }

   public function testInstallPlugin() {
      global $DB;

      $this->setupGLPI();

      $this->assertTrue($DB->connected, "Problem connecting to the Database");

      $this->login('glpi', 'glpi');

      //Drop plugin configuration if exists
      $config = new Config();
      $config->deleteByCriteria(array('context' => 'flyvemdm'));

      // Drop tables of the plugin if they exist
      $query = "SHOW TABLES";
      $result = $DB->query($query);
      while ($data = $DB->fetch_array($result)) {

         if (strstr($data[0], "glpi_plugin_flyvemdm") !== false) {
            $DB->query("DROP TABLE ".$data[0]);
         }
      }

      self::resetGLPILogs();

      $plugin = new Plugin();
      $plugin->getFromDBbyDir("flyvemdm");

      ob_start(function($in) { return ''; });
      $plugin->install($plugin->fields['id']);
      ob_end_clean();

      $PluginDBTest = new PluginDB();
      $PluginDBTest->checkInstall("flyvemdm", "install");

      // Enable the plugin
      $plugin->activate($plugin->fields['id']);
      $this->assertTrue($plugin->isActivated("flyvemdm"), "Cannot enable the plugin");

      // Enable debug mode for enrollment messages
      Config::setConfigurationValues('flyvemdm', ['debug_enrolment' => '1']);

      // Force the MQTT backend's credentials
      // Useful to force the credientials to be the same as a development database
      // and not force broker's reconfiguration when launching tests on the test-dedicates DB
      $mqttUser = new PluginFlyvemdmMqttuser();
      if (!empty(PHPUNIT_FLYVEMDM_MQTT_PASSWD)) {
         $mqttUser->getByUser('flyvemdm-backend');
         $mqttUser->update([
               'id'        => $mqttUser->getID(),
               'password'  => PHPUNIT_FLYVEMDM_MQTT_PASSWD
         ]);
         Config::setConfigurationValues('flyvemdm', ['mqtt_passwd' => PHPUNIT_FLYVEMDM_MQTT_PASSWD]);
      }
   }

   public function testConfigurationExists() {
      $config = Config::getConfigurationValues('flyvemdm');
      $expected = [
            'mqtt_broker_address',
            'mqtt_broker_internal_address',
            'mqtt_broker_port',
            'mqtt_broker_tls',
            'mqtt_use_client_cert',
            'mqtt_broker_tls_ciphers',
            'mqtt_user',
            'mqtt_passwd',
            'instance_id',
            'registered_profiles_id',
            'guest_profiles_id',
            'service_profiles_id',
            'debug_enrolment',
            'debug_noexpire',
            'ssl_cert_url',
            'version',
            'default_device_limit',
            'default_agent_url',
            'computertypes_id',
      ];
      $diff = array_diff_key(array_flip($expected), $config);
      $this->assertCount(0, $diff);

      return $config;
   }

   /**
    * @depends testConfigurationExists
    */
   public function testGuestProfileExists($config) {
      $guestProfileId = $config['guest_profiles_id'];
      $profile = new Profile();
      $this->assertTrue($profile->getFromDB($guestProfileId));
   }

}
