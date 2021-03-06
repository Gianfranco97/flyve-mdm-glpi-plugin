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

class PluginFlyvemdmPolicyRemoveFileIntegrationTest extends RegisteredUserTestCase {

   public function testInitCreateFile() {
      global $DB;

      // Create a file (directly in DB)
      $fileName = 'flyve-user-manual.pdf';
      $fileTable = PluginFlyvemdmFile::getTable();
      $entityId = $_SESSION['glpiactive_entity'];
      $query = "INSERT INTO $fileTable (
         `name`,
         `source`,
         `entities_id`
      )
      VALUES (
         '$fileName',
         '2/12345678_flyve-user-manual.pdf',
         '$entityId'
      )";
      $DB->query($query);
      $mysqlError = $DB->error();
      $file = new PluginFlyvemdmFile();
      $this->assertTrue($file->getFromDBByQuery("WHERE `name`='$fileName'"), $mysqlError);

      return $file;
   }

   public function testInitGetDestination() {
      return "%SDCARD%/path/to/file.pdf";
   }

   public function testInitCreateFleet() {
      // Create a fleet
      $entityId = $_SESSION['glpiactive_entity'];
      $fleet = new PluginFlyvemdmFleet();
      $fleet->add([
            'name'            => 'managed fleet',
            'entities_id'     => $entityId,
      ]);
      $this->assertFalse($fleet->isNewItem());

      return $fleet;
   }

   public function testGetPolicyData() {
      $policyData = new PluginFlyvemdmPolicy();
      $this->assertTrue($policyData->getFromDBBySymbol('removeFile'));

      return $policyData;
   }

   /**
    * @depends testGetPolicyData
    */
   public function testGetRemoveFilePolicy($policyData) {
      $policyFactory = new PluginFlyvemdmPolicyFactory();
      $policy = $policyFactory->createFromPolicy($policyData);
      $this->assertInstanceOf('PluginFlyvemdmPolicyRemovefile', $policy);

      return $policy;
   }

   /**
    * @depends testInitCreateFleet
    * @depends testGetPolicyData
    * @depends testInitCreateFile
    * @depends testInitGetDestination
    */
   public function testApplyPolicyIncomplete(PluginFlyvemdmFleet $fleet, PluginFlyvemdmPolicy $policyData, PluginFlyvemdmFile $file, $destination) {
      $fleet_policy = new PluginFlyvemdmFleet_Policy();
      $fleet_policy->add([
            'plugin_flyvemdm_policies_id' => $policyData->getID(),
            'value'                       => $destination
      ]);
      $this->assertTrue($fleet_policy->isNewItem());
   }

   /**
    * @depends testInitCreateFleet
    * @depends testGetPolicyData
    * @depends testInitCreateFile
    * @depends testInitGetDestination
    */
   public function testApplyPolicyWithBadValue(PluginFlyvemdmFleet $fleet, PluginFlyvemdmPolicy $policyData, PluginFlyvemdmFile $file, $destination) {
      $fleet_policy = new PluginFlyvemdmFleet_Policy();
      $fleet_policy->add([
            'plugin_flyvemdm_fleets_id'   => $fleet->getID(),
            'plugin_flyvemdm_policies_id' => '-1',
            'value'                       => $destination
      ]);
      $this->assertTrue($fleet_policy->isNewItem());
   }

   /**
    * @depends testInitCreateFleet
    * @depends testGetPolicyData
    * @depends testInitCreateFile
    * @depends testInitGetDestination
    */
   public function testApplyPolicy(PluginFlyvemdmFleet $fleet, PluginFlyvemdmPolicy $policyData, PluginFlyvemdmFile $file, $destination) {
      global $DB;

      $table = PluginFlyvemdmMqttupdatequeue::getTable();
      $this->assertTrue($DB->query("TRUNCATE TABLE `$table`"));

      $groupName = $policyData->getField('group');
      $fleetId = $fleet->getID();

      $fleet_policy = new PluginFlyvemdmFleet_Policy();
      $fleet_policy->add([
            'plugin_flyvemdm_fleets_id'   => $fleet->getID(),
            'plugin_flyvemdm_policies_id' => $policyData->getID(),
            'value'                       => $destination
      ]);
      $this->assertFalse($fleet_policy->isNewItem());

      $mqttUpdateQueue = new PluginFlyvemdmMqttupdatequeue();
      $rows = $mqttUpdateQueue->find("`group` = '$groupName'
            AND `plugin_flyvemdm_fleets_id` = '$fleetId'
            AND `status` = 'queued'");
      $this->assertCount(1, $rows);
   }
}