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
 * Processor file for user exclusion overview and configuration
 *
 * File         msgdef.php
 * Encoding     UTF-8
 *
 * @package     tool_usersuspension
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('toolusersuspension');
$context = \context_system::instance();

$msgtype = optional_param('msg', null, PARAM_ALPHA);
$action = optional_param('action', null, PARAM_ALPHA);
$params = [];
if (!empty($msgtype)) {
    $params['msg'] = $msgtype;
}
if (!empty($action)) {
    $params['action'] = $action;
}

$thispageurl = new moodle_url('/' . $CFG->admin . '/tool/usersuspension/view/msgdef.php', $params);

require_capability('tool/usersuspension:administration', $context);

$currenttab = empty($msgtype) ? 'msgdef' : "msgdef_{$msgtype}";
$customcurrent = false;
if (!empty($msgtype)) {
    $customcurrent = get_config('tool_usersuspension', 'msgspec:' . $msgtype);
}

$form = null;

// Process deletion.
if ($action === 'delete' && !empty($msgtype)) {
    $customdata = [];
    $form = new \tool_usersuspension\forms\msgdeleteform($thispageurl, $customdata);
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/' . $CFG->admin . '/tool/usersuspension/view/msgdef.php', ['msg' => $msgtype]));
    } else if ($data = $form->get_data()) {
        // Remove specialisation.
        if ($data->confirm) {
            set_config('msgspec:' . $msgtype, null, 'tool_usersuspension');
        }
        redirect(new moodle_url('/' . $CFG->admin . '/tool/usersuspension/view/msgdef.php', ['msg' => $msgtype]));
    }
}

// Or otherwise we display the edit/adjust form.
if (empty($action) && !empty($msgtype)) {
    // Edit form.
    $vars = \tool_usersuspension\util::get_variables_for_msg($msgtype, true);
    $customdata = ['msgtype' => $msgtype, 'customcurrent' => $customcurrent, 'vars' => $vars];
    $form = new \tool_usersuspension\forms\msgspecform($thispageurl, $customdata);
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/' . $CFG->admin . '/tool/usersuspension/view/msgdef.php', ['msg' => $msgtype]));
    } else if ($data = $form->get_data()) {
        // Add/change specialisation.
        set_config('msgspec:' . $msgtype, $data->content['text'], 'tool_usersuspension');
        redirect(new moodle_url('/' . $CFG->admin . '/tool/usersuspension/view/msgdef.php', ['msg' => $msgtype]));
    }
}

echo $OUTPUT->header();
echo '<div class="tool-usersuspension-container">';
echo '<div>';
\tool_usersuspension\util::print_view_tabs([], $currenttab);
echo '</div>';
echo '<div>' . get_string('page:view:msgdef.php:introduction', 'tool_usersuspension') . '</div>';
if (!empty($msgtype) && empty($action)) {
    echo '<div class="d-flex flex-column mb-3">';
    echo '<div class="d-flex flex-row">';
    echo '<div><strong>' . get_string('msgspec:current', 'tool_usersuspension') . '</strong></div>';
    if ($customcurrent !== false) {
        echo '<div class="ml-auto">' . html_writer::link(
            new moodle_url(
                '/' . $CFG->admin . '/tool/usersuspension/view/msgdef.php',
                ['msg' => $msgtype, 'action' => 'delete']
            ),
            get_string('msgspec:current:delete', 'tool_usersuspension')
        ) . '</div>';
    }
    echo '</div>';
    echo '</div>';
}
if ($form instanceof moodleform) {
    $form->display();
}
echo '</div>';
echo $OUTPUT->footer();
