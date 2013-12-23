#!/usr/bin/env php
<?php
/*
 * Copyright (C) 2013- Sam Stenvall
 *
 * This Program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 *
 * This Program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with XBMC; see the file COPYING. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 */

abstract class Configurable
{

	public function configure(array $properties)
	{
		foreach ($properties as $property=> $value)
			$this->{$property} = $value;
	}

}

class Adapter extends Configurable implements Countable
{

	public $automux;
	public $extrapriority;
	public $full_mux_rx;
	public $grace_period;
	public $idleclose;
	public $idlescan;
	public $name;
	public $nitoid;
	public $op;

	public function setEnabled()
	{
		$this->enabled = 'on';
	}

	public function setDisabled()
	{
		
	}

	public function count()
	{
		return count(get_object_vars($this));
	}

}

class Tvheadend extends Configurable
{

	public $hostname;
	public $url;
	public $adapterUrlName;
	public $username;
	public $password;

	public function getUrl()
	{
		return 'http://'.$this->hostname.':'.$this->port.
				'/dvb/adapter/'.$this->adapterUrlName;
	}

}

// configure adapter and tvheadend instance
$adapter = new Adapter();
$adapter->configure(array(
	'automux'=>'on',
	'extrapriority'=>50,
	'full_mux_rx'=>0,
	'grace_period'=>0,
	'idleclose'=>'on',
	'idlescan'=>'on',
	'name'=>'Philips TDA10023 DVB-C',
	'nitoid'=>0,
	'op'=>'save',
));

$tvheadend = new Tvheadend();
$tvheadend->configure(array(
	'hostname'=>'localhost',
	'port'=>9981,
	'adapterUrlName'=>'_dev_dvb_adapter2_Philips_TDA10023_DVB_C',
	'username'=>'admin',
	'password'=>'',
));

// parse command-line parameters
if ($argc != 2 || !in_array($argv[1], array('--enable', '--disable')))
	die('usage: '.__FILE__." --enable|--disable\n");

switch ($argv[1])
{
	case '--enable':
		$adapter->setEnabled();
		break;
	case '--disable':
		$adapter->setDisabled();
		break;
}

// perform the request
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $tvheadend->getUrl());
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, $tvheadend->username.':'.$tvheadend->password);
curl_setopt($curl, CURLOPT_POST, count($tvheadend));
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($adapter));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($curl);
if ($result === false)
	die("Request failed, check your configuration\n");

$result = json_decode($result);

// exit properly
return (int)!(isset($result->success) && $result->success === 1);
