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

include ('../../../inc/includes.php');
$plugin = new Plugin();
if (!$plugin->isActivated('flyvemdm')) {
   Html::displayNotFoundError();
}

Session::checkRight("flyvemdm:flyvemdm", PluginFlyvemdmProfile::RIGHT_FLYVEMDM_USE);

if (!isset($_GET["id"])) {
   $_GET["id"] = "";
}

if (!isset($_GET["withtemplate"])) {
   $_GET["withtemplate"] = "";
}

$agent = new PluginFlyvemdmAgent();
if (isset($_POST["add"])) {
   $agent->check(-1, CREATE, $_POST);
   if ($newID = $agent->add($_POST)) {
      if ($_SESSION['glpibackcreated']) {
         Html::redirect($agent->getFormURL() . "?id=" . $newID);
      }
   }
   Html::back();
} else if (isset($_POST["update"])) {
   $agent->check($_POST['id'], UPDATE);
   $agent->update($_POST);
   Html::back();
} else if (isset($_POST["ping"])) {
   $agent->check($_POST['id'], UPDATE);
   $agent->update([
         'id'        => $_POST['id'],
         '_ping'     => '',
   ]);
   Html::back();
} else if (isset($_POST["geolocate"])) {
   $agent->check($_POST['id'], UPDATE);
   $agent->update([
         'id'           => $_POST['id'],
         '_geolocate'   => '',
   ]);
   Html::back();
} else if (isset($_POST["inventory"])) {
   $agent->check($_POST['id'], UPDATE);
   $agent->update([
         'id'           => $_POST['id'],
         '_inventory'   => '',
   ]);
   Html::back();
} else if (isset($_POST["purge"])) {
   $agent->check($_POST['id'], PURGE);
   $agent->delete($_POST, 1);
   $agent->redirectToList();
} else if (isset($_POST['unenroll'])) {
   $agent->check($_POST['id'], UPDATE);
   $agent->update([
         'id'           => $_POST['id'],
         '_unenroll'   => '',
   ]);
   Html::back();
} else {
   $agent->check($_GET['id'], READ);
   Html::header(
         PluginFlyvemdmAgent::getTypeName(Session::getPluralNumber()),
         '',
         'admin',
         'PluginFlyvemdmMenu',
         'agent'
   );
   $agent->display(array('id' => $_GET['id'],
         'withtemplate' => $_GET['withtemplate']));

   // Footer
   if (strstr($_SERVER['PHP_SELF'], 'popup')) {
      Html::popFooter();
   } else {
      Html::footer();
   }
}
