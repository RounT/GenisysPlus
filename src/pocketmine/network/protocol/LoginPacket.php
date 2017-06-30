<?php

/*
 *
*  ____            _        _   __  __ _                  __  __ ____
* |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
* | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
* |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
* |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* @author PocketMine Team
* @link http://www.pocketmine.net/
*
*
*/

namespace pocketmine\network\protocol;

#include <rules/DataPacket.h>

class LoginPacket extends DataPacket {

	const NETWORK_ID = Info::LOGIN_PACKET;
	const EDITION_POCKET = 0;

	public $username;
	public $protocol;
	public $gameEdition;
	public $clientUUID;
	public $clientId;
	public $identityPublicKey;
	public $serverAddress;
	public $adRole;
	public $currentInputMode;
	public $defaultInputMode;
	public $deviceModel;
	public $deviceOS;
	public $gameVersion;
	public $guiScale;
	public $tenantId;
	public $uiProfile;

	public $skinId;
	public $skin = "";

	public $clientData = [];

	public function canBeSentBeforeLogin() : bool {
		return true;
	}

	public function decode(){
		$this->protocol = $this->getInt();

		if($this->protocol !== Info::CURRENT_PROTOCOL){
			$this->buffer = null;
			return;
		}
		$this->gameEdition = $this->getByte();
		$this->setBuffer($this->getString(), 0);
		
		$chainData = json_decode($this->get($this->getLInt()));
		foreach($chainData->{"chain"} as $chain){
			$webtoken = $this->decodeToken($chain);
			if(isset($webtoken["extraData"])){
				if(isset($webtoken["extraData"]["displayName"])){
					$this->username = $webtoken["extraData"]["displayName"];
				}
				if(isset($webtoken["extraData"]["identity"])){
					$this->clientUUID = $webtoken["extraData"]["identity"];
				}
				if(isset($webtoken["identityPublicKey"])){
					$this->identityPublicKey = $webtoken["identityPublicKey"];
				}
			}
		}

		$this->clientData = $this->decodeToken($this->get($this->getLInt()));
		$this->clientId = $this->clientData["ClientRandomId"] ?? null;
		$this->serverAddress = $this->clientData["ServerAddress"] ?? null;
		$this->skinId = $this->clientData["SkinId"] ?? null;

		if(isset($this->clientData["SkinData"])){
			$this->skin = base64_decode($this->clientData["SkinData"]);
		}
		if(isset($this->clientData["AdRole"])){
			$this->adRole = $this->clientData["AdRole"];
		}
		if(isset($this->clientData["CurrentInputMode"])){
			$this->currentInputMode =$this->clientData["CurrentInputMode"];
		}
		if(isset($this->clientData["DefaultInputMode"])){
			$this->defaultInputMode = $this->clientData["DefaultInputMode"];
		}
		if(isset($this->clientData["DeviceModel"])){
			$this->deviceModel = $this->clientData["DeviceModel"];
		}
		if(isset($this->clientData["DeviceOS"])){
			$this->deviceOS = $this->clientData["DeviceOS"];
		}
		if(isset($this->clientData["GameVersion"])){
			$this->gameVersion = $this->clientData["GameVersion"];
		}
		if(isset($this->clientData["SkinData"])){
			$this->skin = base64_decode($this->clientData["SkinData"]);
		}
		if(isset($this->clientData["TenantId"])){
			$this->tenantId = $this->clientData["TenantId"];
		}
		if(isset($this->clientData["UIProfile"])){
			$this->uiProfile = $this->clientData["UIProfile"];
		}
	}

	public function encode(){
		// TODO
	}

	public function decodeToken($token){
		$tokens = explode(".", $token);
		list($headB64, $payloadB64, $sigB64) = $tokens;
		return json_decode(base64_decode($payloadB64), true);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLogin($this);
	}
}
