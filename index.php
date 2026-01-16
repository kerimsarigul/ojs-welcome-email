<?php
/**
 * @file plugins/generic/welcomeEmail/index.php
 *
 * Copyright (c) 2026 OJS Services
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Wrapper for Welcome Email plugin.
 */

require_once('WelcomeEmailPlugin.inc.php');
return new WelcomeEmailPlugin();
