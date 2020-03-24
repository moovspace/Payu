<?php
namespace Payu\Order;
use Payu\Util\Email;

class CartOrder
{
	protected $Order = null;

	function Get()
	{
		return $this->Order;
	}

	/**
	 * Set confirmation url (success or error html page)
	 *
	 * @param string $url
	 * @return object
	 */
	function UrlContinue($url)
	{
		if(empty($url))
		{
			throw new Exception("ERR_URL_RETURN", 1);
		}

		$this->UrlContinue = (string) $url;
		return $this;
	}

	/**
	 * Payment confirmation script url
	 *
	 * @param string $url
	 * @return object
	 */
	function UrlNotify($url)
	{
		if(empty($url))
		{
			throw new Exception("ERR_URL_RETURN", 1);
		}

		$this->UrlNotify = (string) $url;
		return $this;
	}

	function Add($extOrderId = '', $totalAmount = 0.00, $description = 'No description', $currencyCode = 'PLN', $posId = '', $customerIp = '127.0.0.1')
	{
		if($currencyCode == 'HUF'){
			// throw new Exception("ERR_HUF_CURRENCY_NOT_ALLOWED", 1);
		}
		if(empty($extOrderId))
		{
			throw new Exception("ERR_EXTORDERID", 1);
		}
		if(empty($posId))
		{
			throw new Exception("ERR_POSID", 1);
		}

		// Urls
		$this->Order['notifyUrl'] = $this->UrlNotify;
		$this->Order['continueUrl'] = $this->UrlContinue;
		// Pos
		$this->Order['merchantPosId'] = $posId;
		// Order
		$this->Order['extOrderId'] = $extOrderId;
		$this->Order['totalAmount'] = $totalAmount;
		$this->Order['currencyCode'] = $currencyCode;
		$this->Order['description'] = $description;
		$this->Order['customerIp'] = $customerIp;
	}

	function AddProduct($name, $unitPrice, $quantity)
	{
		// Create new product
		$product = ['name' => $name, 'unitPrice' => (int) $unitPrice, 'quantity' => (int) $quantity];
		// Add product to list
		$this->Order['products'][] = $product;
		return $this;
	}

	function AddBuyer($email, $phone, $firstName, $lastName, $lang = 'pl')
	{
		Email::IsValidEmail($email);

		$this->Order['buyer']['email'] = $email;
		$this->Order['buyer']['phone'] = $phone;
		$this->Order['buyer']['firstName'] = $firstName;
		$this->Order['buyer']['lastName'] = $lastName;
		$this->Order['buyer']['language'] = $lang;
	}
}