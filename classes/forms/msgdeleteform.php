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
 * this file contains the CSV upload form class
 *
 * File         upload.php
 * Encoding     UTF-8
 *
 * @package     tool_usersuspension
 *
 * @copyright   Sebsoft.nl
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usersuspension\forms;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/formslib.php');

/**
 * tool_usersuspension\forms\upload
 *
 * @package     tool_usersuspension
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class msgdeleteform extends \moodleform {
    /**
     * form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_ALPHA);
        $mform->setConstant('action', 'delete');

        $mform->addElement(
            'static',
            '_confirm',
            '',
            '<div class="alert alert-info">' .
            get_string('msgspec:current:delete:help', 'tool_usersuspension') . '</div>'
        );

        $mform->addElement(
            'advcheckbox',
            'confirm',
            get_string('msgspec:current:delete', 'tool_usersuspension')
        );

        $this->add_action_buttons(
            true,
            get_string('csv:upload:continue', 'tool_usersuspension')
        );
    }
}
