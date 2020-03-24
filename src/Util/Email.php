<?php
namespace Payu\Util;

final class Email
{
	private $email;

	function __construct(string $email)
	{
		$this->IsValidEmail($email);
		$this->email = $email;
	}

	function __toString(): string
	{
		return $this->email;
	}

	static function FromString(string $email): self
	{
		return new self($email);
	}

	static function IsValidEmail(string $email): void
	{
		if(preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email) != 1)
		{
			throw new Exception("ERR_EMAIL", 1);
		}
	}

	static function IsValidMx($email)
	{
		if (!checkdnsrr(end(explode('@', $email)), 'MX'))
		{
			throw new Exception("ERR_EMAIL_MX", 1);
		}
	}
}
?>
