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
class msgspecform extends \moodleform {
    /**
     * form definition
     */
    public function definition() {
        global $USER;
        $mform = $this->_form;

        $mform->addElement('hidden', 'msg');
        $mform->setType('msg', PARAM_ALPHA);
        $mform->setDefault('msg', $this->_customdata['msgtype']);

        if ($this->_customdata['customcurrent'] === false) {
            $mform->addElement(
                'static',
                '_content',
                '',
                '<div class="alert alert-warning">' .
                get_string('msgspec:current:none', 'tool_usersuspension') . '</div>'
            );
        }

        $basemsg = '';
        switch ($this->_customdata['msgtype']) {
            case 'suspend':
                $preview = \tool_usersuspension\util::get_user_suspended_email($USER);
                $basemsg = get_string('email:user:suspend:auto:body', 'tool_usersuspension');
                break;
            case 'unsuspend':
                $preview = \tool_usersuspension\util::get_user_unsuspended_email($USER);
                $basemsg = get_string('email:user:unsuspend:body', 'tool_usersuspension');
                break;
            case 'delete':
                $preview = \tool_usersuspension\util::get_user_deleted_email($USER);
                $basemsg = get_string('email:user:delete:body', 'tool_usersuspension');
                break;
            case 'warning':
                $preview = \tool_usersuspension\util::get_user_warning_email($USER);
                $basemsg = get_string('email:user:warning:body', 'tool_usersuspension');
                break;
        }

        $content = '';
        $content .= '<div class="alert alert-info mb-2">';
        $content .= '  <table class="table"><tbody>';
        $content .= '    <tr><th colspan="2">' . get_string('preview', 'tool_usersuspension') . '</th></tr>';
        $content .= '    <tr><th>' . get_string('subject') . '</th><td>' . $preview[0] . '</td></tr>';
        $content .= '    <tr><th>' . get_string('body', 'tool_usersuspension') . '</th><td>' . $preview[1] . '</td></tr>';
        $content .= '  </tbody></table>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<div class="d-flex flex-column flex-md-row">';
        $content .= '<div class="alert alert-info">';
        $content .= get_string('msgspec:variables', 'tool_usersuspension', $this->_customdata['vars']);
        $content .= '</div>';
        $content .= '<div class="alert alert-info ml-2">';
        $content .= get_string('langmsg', 'tool_usersuspension', $basemsg);
        $content .= '</div>';
        $content .= '</div>';
        $mform->addElement('static', '_content', '', $content);

        $options = [
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 0,
            'areamaxbytes' => 0,
            'context' => \context_system::instance(),
            'noclean' => 0,
            'trusttext' => 0,
            'return_types' => 15,
            'enable_filemanagement' => false,
            'removeorphaneddrafts' => false,
            'autosave' => false,
        ];
        $mform->addElement('editor', 'content', get_string('content'), $options);
        $mform->setType('content', PARAM_RAW);
        if ($this->_customdata['customcurrent'] !== false) {
            $mform->setDefault(
                'content',
                [
                    'text' => $this->_customdata['customcurrent'],
                    'format' => FORMAT_HTML,
                ]
            );
        }

        $this->add_action_buttons(true, get_string('update'));
    }
}
