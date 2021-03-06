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
 * @author    the flyvemdm plugin team
 * @copyright Copyright © 2017 Teclib
 * @license   AGPLv3+ http://www.gnu.org/licenses/agpl.txt
 * @link      https://github.com/flyve-mdm/flyve-mdm-glpi
 * @link      https://flyve-mdm.com/
 * ------------------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

function plugin_flyvemdm_update_to_dev(Migration $migration) {
   global $DB;

   $table = PluginFlyvemdmEntityconfig::getTable();
   $migration->addField($table, 'support_name', 'text', ['after' => 'agent_token_life']);
   $migration->addField($table, 'support_phone', 'string', ['after' => 'support_name']);
   $migration->addField($table, 'support_website', 'string', ['after' => 'support_phone']);
   $migration->addField($table, 'support_email', 'string', ['after' => 'support_website']);
   $migration->addField($table, 'support_address', 'text', ['after' => 'support_email']);

   // update schema
   $table = PluginFlyvemdmAgent::getTable();
   $migration->addField($table, 'users_id', 'integer', ['after' => 'computers_id']);

   $migration->setVersion(PLUGIN_FLYVEMDM_VERSION);

   $migration->addField(PluginFlyvemdmAgent::getTable(), 'is_online', 'integer', ['after' => 'last_contact']);
}
