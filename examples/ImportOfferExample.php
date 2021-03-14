<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Cosmolot\ApiClient;
use Cosmolot\Exceptions\InvalidRequestException;

/**
 * Initialization params
 */
const DOMAIN = '';
const LOGIN = '';
const PASSWORD = '';
/**
 * Client initialization by 
 * - domain name
 * - login
 * - password
 */
$client = new ApiClient(DOMAIN, LOGIN, PASSWORD);
/**
 * Getting offer from account by offer ID
 */
try
{
    $offerId = 127834;
    $offers = $client->offers()->get();
    /**
     * Use offer object model
     */
    echo '<pre>';
    print_r($offers);
    echo '</pre>';
}
catch(InvalidRequestException $e)
{
    print_r($e);
}