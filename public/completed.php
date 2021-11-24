<?php
/**
 * This file renders the default view and loads the dependencies.
 *
 * @package Fjakkarin/FitnessglNets
 */

namespace Fjakkarin\FitnessglNets;

use Fjakkarin\NetsSample\Inc\Template;

include 'inc/Template.php';

Template::view('../views/completed.html');

