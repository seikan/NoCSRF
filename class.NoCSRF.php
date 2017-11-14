<?php

/**
 * NoCSRF: A simple PHP class to stop Cross-Site Request Forgery (CSRF).
 *
 * Copyright (c) 2017 Sei Kan
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  2017 Sei Kan <seikan.dev@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * @see       https://github.com/seikan/NoCSRF
 */
class NoCSRF
{
	const PASSED = 100;
	const POST_INPUT_NOT_FOUND = 101;
	const TOKEN_NOT_FOUND = 102;
	const TOKEN_INVALID = 103;
	const TOKEN_EXPIRED = 104;
	const IP_CHANGED = 105;

	private $key = '_NoCSRF_RTCYJzsdgx';
	private $lockIP = false;
	private $timer = false;

	/**
	 * Initialize NoCSRF object.
	 *
	 * @param array $options
	 */
	public function __construct($options = [])
	{
		// Start a session
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		if (isset($options['key'])) {
			$this->key = $options['key'];
		}

		if (isset($options['lock_ip']) && is_bool($options['lock_ip'])) {
			$this->lockIP = $options['lock_ip'];
		}

		if (isset($options['timer']) && preg_match('/^\d+$/', $options['timer'])) {
			$this->timer = $options['timer'];
		}

		// Generate new token
		if (!isset($_SESSION[$this->key])) {
			$token = $this->random(32);

			if ($this->lockIP) {
				$token .= md5($this->key . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''));
			}

			if ($this->timer !== false) {
				$token .= strtotime('+' . ((preg_match('/^\d+$/', $this->timer)) ? $this->timer : 600) . ' seconds');
			}

			$_SESSION[$this->key] = base64_encode($token);
		} elseif ($this->timer !== false && $_SERVER['REQUEST_METHOD'] == 'GET') {
			// Update timestamp
			$_SESSION[$this->key] = base64_encode(substr(base64_decode($_SESSION[$this->key]), 0, 64) . strtotime('+' . $this->timer . ' seconds'));
		}
	}

	/**
	 * Validate a form submission.
	 *
	 * @return int
	 */
	public function validate()
	{
		if (!isset($_POST[$this->key])) {
			return self::POST_INPUT_NOT_FOUND;
		}

		if (!isset($_SESSION[$this->key])) {
			return self::TOKEN_NOT_FOUND;
		}

		if ($this->lockIP || $this->timer !== false) {
			$raw = base64_decode($_SESSION[$this->key]);

			$origin = substr($raw, 32, 32);
			$time = substr($raw, 64);

			if ($this->lockIP && $origin != md5($this->key . (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''))) {
				return self::IP_CHANGED;
			}

			if ($this->timer !== false && $time < time()) {
				return self::TOKEN_EXPIRED;
			}
		}

		if ($_POST[$this->key] != $_SESSION[$this->key]) {
			return self::TOKEN_INVALID;
		}

		return self::PASSED;
	}

	/**
	 * Get key.
	 *
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * Get current token.
	 *
	 * @return string
	 */
	public function getToken()
	{
		return (isset($_SESSION[$this->key])) ? $_SESSION[$this->key] : null;
	}

	/**
	 * Delete current token.
	 */
	public function deleteToken()
	{
		unset($_SESSION[$this->key]);
	}

	/**
	 * Output a hidden text field to hold security token.
	 *
	 * @return string
	 */
	public function renderHTML()
	{
		return '<input type="hidden" name="' . $this->key . '" value="' . ((isset($_SESSION[$this->key])) ? $_SESSION[$this->key] : '') . '">';
	}

	/**
	 * Generate a random string.
	 *
	 * @param int $length
	 *
	 * @return string
	 */
	private function random($length)
	{
		$key = '';

		$pattern = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		for ($i = 0; $i < $length; ++$i) {
			$key .= $pattern[mt_rand(0, strlen($pattern) - 1)];
		}

		return $key;
	}
}
