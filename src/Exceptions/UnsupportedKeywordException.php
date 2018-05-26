<?php

/**
 * A simple database class with PHP, PDO and a query builder.
 *
 * The class extends PDO for more control and in order to keep all features of PDO.
 *
 * PHP version >= 7.0
 *
 * LICENSE: MIT, see LICENSE file for more information
 *
 * @package jr-cologne/db-class
 * @author JR Cologne <kontakt@jr-cologne.de>
 * @copyright 2017 JR Cologne
 * @license https://github.com/jr-cologne/db-class/blob/master/LICENSE MIT
 * @version v2.1.2
 * @link https://github.com/jr-cologne/db-class GitHub Repository
 * @link https://packagist.org/packages/jr-cologne/db-class Packagist site
 *
 * ________________________________________________________________________________
 *
 * UnsupportedKeywordException.php
 *
 * An exception which is thrown when an unsupported keyword is used in the method DB::retrieve().
 * 
 */

namespace JRCologne\Utils\Database\Exceptions;

use \Exception;

class UnsupportedKeywordException extends Exception {
  
}
