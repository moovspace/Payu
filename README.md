# Payu płatności composer
Jak dodać płatności PayU do swojej strony internetowej. PayU php REST API.
- Linki płatności (zamówienia) zapisywane w bazie danych
- Powiadomienia z PayU zapisywane w bazie danych
- Zwroty zapisywane w bazie danych

**Po zmianie danych w config.php i imporcie bazy danych i usera wszystko powinno działać na sandboxie.**

## Konto sandbox do testów
https://registration-merch-prod.snd.payu.com/boarding/#/registerSandbox/

## Usatwienia
Włącz auto odbiór w panelu klienta PayU

### Import lib comoser
```bash
"require": {
        "moovspace/payu": "1.0"
},
"repositories": [
	{
		"type": "vcs",
		"url": "https://github.com/moovspace/payu"
	}
],
```

### Update class with composer
```bash
# Pobierz i rozpakuj do twoja.strona.www
cd /var/www/html/twoja.strona.www

# Utwórz autoload
composer update
composer dump-autoload -o
```

### Przykład pliku konfiguracyjnego (Klucze PayU, ustawienia sandbox or secure)
```bash
src/Config.php
```

### Baza danych mysql hasło, tabele i użytkownik (opcjonalnie)
```bash
# Zaimportuj tabele
mysql -u root -p < src/Payment/sql/payu.sql
mysql -u root -p < src/Payment/sql/users.sql

# Przyklad klasy połaczenia z bazą danych
src/Db/Db.php
```

## Link do płatności PayU

### Utwórz link do płatności
Jak utworzyć link do płatności
```bash
http://twoja.strona.www/Example/CreatePaymentLink.php
```

### Zwrot środków zamowienia
Jak dokonać zwrotu transakcji (całego lub częściowego)
```bash
http://twoja.strona.www/Example/OrderRefund.php
```

### Strona potwierdzenia dla klienta (continueUrl)
Strony potwierdzeń dla klienta
```bash
http://twoja.strona.www/Example/notify/success.php
http://twoja.strona.www/Example/notify/error.php
```

### Plik powiadomień dla Payu (notifyUrl)
Plik na który payu wysyła potwierdzenia transakcji
```bash
http://twoja.strona.www/Example/Notifications.php
# Link dodaje się w pliku CreateOrderPaymentLink.php
# $order['notifyUrl'] = ''
```

### Pobierz link do płatności
Example/CreateOrderPaymentLink.php
```php
<?php
require_once 'init.php';
require_once 'vendor/autoload.php';

use \Exception;
use Payu\Config; // Change to your Config.php class
use Payu\Order\Order;
use Payu\Order\CartOrder;
use Payu\Auth\Credentials;

try
{
	$auth = new Credentials();
	$token = $auth->Token(Config::PAYU_POS_ID, Config::PAYU_CLIENT_SECRET, Config::SANDBOX);

	$o = new CartOrder();
	$o->UrlContinue('https://twoja.strona.www/Example/notify/success.php');
	$o->UrlNotify('https://twoja.strona.www/Example/Notifications.php');
	$o->Add(md5(uniqid('', true)), 123456, 'Zamówienie dostawa', 'PLN', Config::PAYU_POS_ID);
	$o->AddProduct('Zamówienie 123456', 123456, 1);
	$o->AddBuyer('email@domain.xx', '+48 100 100 100', 'Anka', 'Specesetka', 'pl');
	$order = $o->Get();

	// Utwórz link do płatności
	$obj = Order::Create((array) $order, $token, Config::SANDBOX);

	if($obj->status == 'SUCCESS')
	{
		echo "</br> OrderId: " . $obj->response->orderId;
		echo "</br> ExtOrderId: " . $obj->response->extOrderId;

		// Link do płatności (lub przekieruj na ten url z header(...);)
		echo '</br> <a href="'.$obj->response->redirectUri.'"> Pay Now </a>';

		// echo "<pre>";
		// print_r($obj->response);
	}
	else
	{
		echo "Ups errors!";
		print_r($obj);
	}

} catch (Exception $e) {
	echo $e->getMessage();
}
?>
```

## Instalacja

### Pobierz repo
Pobierz i rozpakuj w katalogu głównym domeny
```bash
# do katalogu domeny
/var/www/html/twoja.strona.www

# uprawnienia zapisu odczytu server apache2
chown -R www-data:www-data /var/www/html/twoja.strona.www
chmod -R 775 /var/www/html/twoja.strona.www
```

### Uruchom composera
```bash
cd /var/www/html/twoja.strona.www
composer update
composer dump-autoload -o
```

## Jak to działa
- Pobierasz link płatności z payu
- Przekierowujesz klienta na ten link
- Klient dokonuje płatności (potwierdza lub rezygnuje)
- Potwierdzona - przekierowanie na continueUrl: http://twoja.strona.www/Example/notify/success.php
- Rezygnuje/Anulouje - przekierowanie na http://twoja.strona.www/Example/notify/success.php?error=501 => error.php
- Po dokonaniu transakcji payu wysyła potwierdzenie na podany w zamówieniu (w pliku CreatePaymentLink.php) notifyUrl: http://twoja.strona.www/Example/Notifications.php

## Błędy z potwierdzeń (notyfikacji z PayU)
Zobacz w lista transakcji > Szczegóły pojedyńczej transakcji > Pokaż raporty (Zielony przycisk)
