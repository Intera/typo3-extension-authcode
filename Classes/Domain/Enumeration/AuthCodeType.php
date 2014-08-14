<?php
namespace Tx\Authcode\Domain\Enumeration;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "authcode".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\Type\Enumeration;

/**
 * The type of the auth code.
 */
class AuthCodeType extends Enumeration {

	const INDEPENDENT = 'independent';

	const RECORD = 'record';
}