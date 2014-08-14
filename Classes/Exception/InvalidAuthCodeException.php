<?php
namespace Tx\Authcode\Exception;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "authcode".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Exception as DefaultException;

/**
 * This Exception will be thrown when the submitted auth code is invalid and a
 * valid auth code is required.
 */
class InvalidAuthCodeException extends DefaultException {

	/**
	 * Creates the Exception with a predefined message and code.
	 *
	 * @param \Exception $previous
	 */
	public function __construct(\Exception $previous = NULL) {
		parent::__construct('An invalid auth code was submitted', 1408026714, $previous);
	}
}