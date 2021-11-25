<?php


namespace Fjakkarin\NetsSample\inc;


class FormData {

	private $package;
	private $firstName;
	private $lastName;
	private $email;
	private $phone;
	private $postCode;

	/**
	 * @return string
	 */
	public function get_email(): string {
		return $this->email;
	}

	/**
	 * @param string $package
	 *
	 * @return FormData
	 */
	public function set_package( string $package ): static {
		$this->package = $package;

		return $this;
	}

	/**
	 * @param string $firstName
	 *
	 * @return FormData
	 */
	public function set_first_name( string $firstName ): static {
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * @param string $lastName
	 *
	 * @return FormData
	 */
	public function set_last_name( string $lastName ): static {
		$this->lastName = $lastName;

		return $this;
	}

	/**
	 * @param string $email
	 *
	 * @return FormData
	 */
	public function set_email( string $email ): static {
		$this->email = $email;

		return $this;
	}

	/**
	 * @param string $phone
	 *
	 * @return FormData
	 */
	public function set_phone( string $phone ): static {
		$this->phone = $phone;

		return $this;
	}

	/**
	 * @param string $postCode
	 *
	 * @return FormData
	 */
	public function set_post_code( string $postCode ): static {
		$this->postCode = $postCode;

		return $this;
	}
}
