<?php
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
 * This local plugin is being used temporarily to hold install and upgrade
 * scripts that would normally go into core lib/db.  We are not putting them in
 * core yet because we don't want version number conflicts with core upgrades
 * while these gradebook changes are in development.
 */

$plugin->version  = 2014080700;
$plugin->requires = 2013110500;
$plugin->component = 'local_gradebookcoredb';
