// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Javascript support for general plugins overview and management screens
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @namespace
 */
M.core_plugin = M.core_plugin || {};

/**
 * YUI instance holder
 */
M.core_plugin.Y = {};

/**
 * Initialize Javascript support for the plugins check screen during the upgrade
 *
 * @param {Object} Y YUI instance
 */
M.core_plugin.init_plugins_check = function(Y) {
    M.core_plugin.Y = Y;

    Y.all('#plugins-check .unimportant').setStyle('visibility', 'collapse');

    var showall = '<a href="#" id="collapsecontroller-showall">' +
        M.util.get_string('showall', 'core_plugin') + '</a>';
    var showhighlighted = '<a href="#" id="collapsecontroller-showhighlighted">' +
        M.util.get_string('showhighlighted', 'core_plugin') + '</a>';

    Y.one('#collapsecontroller').set('innerHTML', showall + ' | ' + showhighlighted);
    Y.one('#collapsecontroller-showall').on('click', function (e) {
        Y.all('#plugins-check .unimportant').setStyle('visibility', 'visible');
    });
    Y.one('#collapsecontroller-showhighlighted').on('click', function (e) {
        Y.all('#plugins-check .unimportant').setStyle('visibility', 'collapse');
    });
}
