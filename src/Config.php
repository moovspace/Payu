<?php
namespace Payu;

class Config
{
    const SANDBOX = true;

    // Payu credentials
    const PAYU_POS_ID = '';
    // Klucz do weryfikacji podpisu
    const PAYU_MD5_KEY = '';
    // Oauth key
    const PAYU_CLIENT_SECRET = '';

    // Mysql db
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'delivery';
	const MYSQL_PASS = 'toor';
	const MYSQL_PORT = 3306;
	const MYSQL_DBNAME = 'delivery';
}