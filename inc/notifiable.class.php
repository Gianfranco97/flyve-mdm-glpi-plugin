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

if (! defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

/**
 * @since 0.1.31
 */
interface PluginFlyvemdmNotifiable {

   public function getTopic();

   /**
    * get the agents related to the notifiable
    * @return array the final PluginFlyvemdmAgents to be notified
    */
   public function getAgents();

   /**
    * get the fleet attached to the notifiable
    * @return PluginFlyvemdmFleet the fleet associated to the notifiable
    */
   public function  getFleet();

   /**
    * get the applications related to the notifiable
    * @return array of PluginFlyvemdmPackage
    */
   public function getPackages();

   /**
    * get the files related to the notifiable
    * @return array of PluginFlyvemdmFile
    */
   public function getFiles();

   /**
    * Send a MQTT message
    * @param string $topic
    * @param string $mqttMessage
    * @param number $qos
    * @param number $retain
    */
   public function notify($topic, $mqttMessage, $qos = 0, $retain = 0);
}
