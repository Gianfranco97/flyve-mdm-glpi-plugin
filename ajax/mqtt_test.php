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

// Needs to be removed if this entry point needs to autoload a class from the plugin.
//$AJAX_INCLUDE = 1;
include ('../../../inc/includes.php');

//define("PLUGIN_FLYVEMDM_ROOT", $CFG_GLPI['root_doc'] . "/plugins/flyvemdm");

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();
Session::checkLoginUser();

Session::checkRight("entity", UPDATE);

// Check and cleanup $_POST data
$address = false;
if (isset($_POST['mqtt_broker_internal_address'])) {
   if (preg_match('/^[a-zA-Z0-9-\.]*$/', $_POST['mqtt_broker_internal_address'])) {
      $address = $_POST['mqtt_broker_internal_address'];
   }
}

$sslCiphers = $_POST['mqtt_broker_tls_ciphers'];
if (isset($_POST['mqtt_broker_port']) && is_numeric($_POST['mqtt_broker_port'])) {
   $port = $_POST['mqtt_broker_port'];
} else {
   $port = 0;
}

if (isset($_POST['mqtt_broker_tls']) && $_POST['mqtt_broker_tls'] != '0') {
   $isTls = true;
} else {
   $isTls = false;
}


if ($port < 1 || $port > 65535) {
   $port = false;
}

if ($address === false || $port === false) {
   echo '{"status" : "ko"}';
   exit();
}
$clientid = "flyvemdm-test";
$mqttClient = PluginFlyvemdmMqttclient::getInstance();
if ($mqttClient->sendTestMessage($address, $port, $isTls, $sslCiphers)) {
   echo json_encode(array("status" => __('Test message sent', 'flyvemdm')), JSON_UNESCAPED_SLASHES);
} else {
   echo json_encode(array("status" => __('Test message not sent', 'flyvemdm')), JSON_UNESCAPED_SLASHES);
}
