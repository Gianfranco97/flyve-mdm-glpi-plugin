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

use Glpi\test\CommonTestCase;

class PluginFlyvemdmPolicyFileDeploymentTest extends CommonTestCase {

   public function setUp() {
      parent::setUp();
      self::setupGLPIFramework();
      self::login('glpi', 'glpi', true);
   }


   public function testInitCreatePolicy() {
      $policyData = new PluginFlyvemdmPolicy();
      $policyData->fields = [
            'group'     => 'file',
            'symbol'    => 'deployFile',
            'type_data' => '',
            'unicity'   => '0',
      ];
      $policy = new PluginFlyvemdmPolicyDeployFile($policyData);
      $this->assertInstanceOf('PluginFlyvemdmPolicyDeployFile', $policy);

      return $policy;
   }

   public function testInitCreateFile() {
      global $DB;

      $table_file = PluginFlyvemdmFile::getTable();
      $query = "INSERT INTO `$table_file` (`name`) VALUES ('filename.ext')";
      $result = $DB->query($query);
      $this->assertNotFalse($result);

      $file = new PluginFlyvemdmFile();
      $file->getFromDB($DB->insert_id());
      $this->assertFalse($file->isNewItem());

      return $file;
   }

   public function providePaths() {
      return array(
            array(
                  'data'      => array(
                        '%SDCARD%/'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '%SDCARD%/../'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '%SDCARD%'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '%SDCARD%/file.ext'
                  ),
                  'expected'  => true,
            ),
            array(
                  'data'      => array(
                        '%SDCARD%/../file.ext'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '/file.ext'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        'file.ext'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        ''
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '/folder/file.ext'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '/%SDCARD%//file.ext'
                  ),
                  'expected'  => false,
            ),
            array(
                  'data'      => array(
                        '/../file.ext'
                  ),
                  'expected'  => false,
            ),

      );
   }

   /**
    * @dataProvider providePaths
    * @depends testInitCreatePolicy
    * @depends testInitCreateFile
    */
   public function testPathIntegrity($data, $expected, $policy, $item) {
      $value = json_encode(array(
            'destination'        => $data,
            'remove_on_delete'   => 0,
      ), JSON_UNESCAPED_SLASHES);
      $success = $policy->integrityCheck($value, $item->getType(), $item->getId());
   }

}