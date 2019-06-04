<?php
/**
 * Created by PhpStorm.
 * User: oleksandr
 * Date: 01.06.19
 * Time: 20:22
 */

class PinsRepository {
	protected $wpdb;

	public function __construct(\wpdb $wpdb) {
		$this->wpdb = $wpdb;
	}

	public function getPin() {}

	public function addPin() {}

	public function removePin() {}

	public function search($args) {
		$args = wp_parse_args($args, []);
	}

	protected function getTableName() {
		return "{$this->wpdb->prefix}_pins";
	}
}
