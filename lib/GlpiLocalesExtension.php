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

class GlpiLocalesExtension extends \Twig_Extension
{
   /**
    * Sets aliases for functions
    *
    * @see Twig_Extension::getFunctions()
    * @return array
    */
   public function getFunctions() {
      return array(
            new \Twig_SimpleFunction('__', '__'),
            new \Twig_SimpleFunction('__s', '__s'),
            new \Twig_SimpleFunction('_e', '_e'),
            new \Twig_SimpleFunction('_ex', '_ex'),
            new \Twig_SimpleFunction('_n', '_n'),
            new \Twig_SimpleFunction('_nx', '_nx'),
            new \Twig_SimpleFunction('_sn', '_sn'),
            new \Twig_SimpleFunction('_sx', '_sx'),
            new \Twig_SimpleFunction('_x', '_x'),
      );
   }

   /**
    * Returns the name of the extension.
    *
    * @return string The extension name
    *
    * @see Twig_ExtensionInterface::getName()
    */
   public function getName() {
      return 'glpi_locales_extension';
   }
}