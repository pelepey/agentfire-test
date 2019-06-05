<?php
/**
 * Created by PhpStorm.
 * User: oleksandr
 * Date: 01.06.19
 * Time: 20:22
 */

/**
 * Class PinsRepository
 */
class PinsRepository {
	protected $wpdb;

    /**
     * PinsRepository constructor.
     * @param wpdb $wpdb
     */
	public function __construct(\wpdb $wpdb)
    {
		$this->wpdb = $wpdb;
	}

    /**
     * @param int $id
     *
     * @return array
     */
	public function getPin($id)
    {
	    return [];
    }

    /**
     * @param array $pin
     *
     * @return array
     */
	public function addPin(array $pin)
    {
	    return [];
    }

    /**
     * @param $id
     */
	public function removePin($id)
    {

    }

    /**
     * @return string
     */
    public function getTableName() {
		return "{$this->wpdb->prefix}pins";
	}
}
