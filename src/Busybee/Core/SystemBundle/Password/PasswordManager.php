<?php
namespace Busybee\Core\SystemBundle\Password;

class PasswordManager
{

	public function buildPassword($params)
	{
		$password = new \stdClass();

		foreach ($params as $name => $value)
			$password->$name = $value;

		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789![]{}()%&*$#^<>~@|";
		$text  = "";
		for ($i = 0; $i < $password->minLength + 4; $i++)
		{
			if ($i == 0)
				$text .= substr($chars, rand(1, 26) - 1, 1);
			elseif ($i == 1)
				$text .= substr($chars, rand(1, 26) + 25, 1);
			else if ($i == 2)
				$text .= substr($chars, rand(1, 10) + 51, 1);
			else if ($i == 3)
				$text .= substr($chars, rand(1, 19) + 61, 1);
			else
				$text .= substr($chars, rand(1, strlen($chars)), 1);
		}

		$password->text = $text;

		$pattern = "^(.*";
		if ($password->mixedCase)
		{
			$pattern             .= "(?=.*[a-z])(?=.*[A-Z])";
			$password->mixedCase = $password->mixedCase ? 'checked' : '';
		}
		$password->mixedCaseText = $password->mixedCase ? 'Yes' : 'No';
		if ($password->numbers)
		{
			$pattern           .= "(?=.*[0-9])";
			$password->numbers = $password->numbers ? 'checked' : '';
		}
		$password->numbersText = $password->numbers ? 'Yes' : 'No';
		if ($password->specials)
		{
			$pattern            .= "(?=.*?[#?!@$%^&*-])";
			$password->specials = $password->specials ? 'checked' : '';
		}
		$password->specialsText = $password->specials ? 'Yes' : 'No';
		$pattern                .= ".*){" . $password->minLength . ",}$";

		$password->pattern = $pattern;

		return $password;
	}
}