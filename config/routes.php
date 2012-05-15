<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

use lithium\net\http\Router;

Router::connect('/login/github', array('library' => 'li3_auth_github', 'controller' => 'li3_auth_github.auth', 'action' => 'check'));
Router::connect('/login/github/requested', array('library' => 'li3_auth_github', 'controller' => 'li3_auth_github.auth', 'action' => 'requested'));
Router::connect('/callback/github', array('library' => 'li3_auth_github', 'controller' => 'li3_auth_github.auth', 'action' => 'requested'));

?>
