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

namespace mod_coursework\allocation\table;

/**
 * Class file for the renderable object that makes the table for allocating markerts to students.
 *
 * @package    mod
 * @subpackage coursework
 * @copyright  2011 University of London Computer Centre {@link ulcc.ac.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_coursework\models\coursework;
use mod_coursework\allocation\table\row\builder as row_builder;
use mod_coursework\render_helpers\grading_report\cells\allocatable_cell;
use mod_coursework\render_helpers\grading_report\cells\group_cell;
use mod_coursework\render_helpers\grading_report\cells\user_cell;
use mod_coursework\stages\base as stage_base;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents the table that will show all students and all markers so that they can be matched up with one another for grading.
 * Various automatic strategies will be available for this, but the manual override happens here.
 */
class builder {

    /**
     * @var coursework
     */
    private $coursework;

    /**
     * @var array The sorting options etc for the table.
     */
    private $options;

    /**
     * Constructor makes a new instance.
     *
     * @param coursework $coursework
     * @param array $options
     */
    public function __construct($coursework, array $options) {
        $this->coursework = $coursework;
        $this->options = $options;
    }


    /**
     * Takes the raw data, instantiates each row as a new renderable object and returns the whole lot.
     *
     * @return array
     */
    public function get_rows() {

        $allocatables = $this->get_coursework()->get_allocatables();

        $rows = array();
        foreach ($allocatables as $allocatable) {
            $rows[] = new row_builder($this, $allocatable);
        }

        return $rows;

    }

    /**
     * Getter for the coursework instance
     *
     * @return coursework
     */
    public function get_coursework() {
        return $this->coursework;
    }

    /**
     * Returns an array of options for retrieving SQL to fill the table e.g. sort.
     *
     * @return array
     */
    public function get_options() {
        return $this->options;
    }

    /**
     * @return stage_base[]
     */
    public function marking_stages() {
        return $this->get_coursework()->marking_stages();
    }

    /**
     * @return allocatable_cell
     */
    public function get_allocatable_cell() {
        $items = array(
            'coursework' => $this->coursework
        );

        if ($this->coursework->is_configured_to_have_group_submissions()) {
            return new group_cell($items);
        }
        return new user_cell($items);
    }
}
