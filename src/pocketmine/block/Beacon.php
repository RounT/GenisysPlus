<?php

 /*
 *  _______                                     ______  _
 * /  ____ \                                   |  __  \| \
 * | |    \_|              _                   | |__| || |
 * | |   ___  ___  _  ___ (_) ___  __    _ ___ |  ____/| | _   _  ___
 * | |  |_  |/(_)\| '/_  || |/___\(_)\  ///___\| |     | || | | |/___\
 * | \___|| | |___| |  | || |_\_\   \ \// _\_\ | |     | || | | |_\_\
 * \______/_|\___/|_|  |_||_|\___/   \ /  \___/|_|     |_||__/,_|\___/
 *                                   //
 *                                  (_)                Power by:
 *                                                           Tesseract
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @由Tessetact团队创建，GenisysPlus项目组修改
 * @链接 https://github.com/TesseractTeam
 * @链接 https://github.com/Tcanw/GenisysPlus
 *
 */
 
namespace pocketmine\block;



use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Tile;
use pocketmine\tile\Beacon as TileBeacon;


class Beacon extends Transparent{
 
 	protected $id = self::BEACON;
 
 	public function __construct($meta = 0){
 		$this->meta = $meta;
 	}
 
 	public function canBeActivated() : bool{
 		return true;
 	}
 
 	public function getName(){
 		return "Beacon";
 	}
	
	public function getLightLevel(){
		return 15;
	}
	
	public function getResistance() {
		return 15;
	}
	
	public function getHardness() {
		return 3;
	}
 
 	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
 		$this->getLevel()->setBlock($this, $this, true, true);
 		$nbt = new CompoundTag("", [
 			new StringTag("id", Tile::BEACON),
			new ByteTag("isMovable", (bool) false),
			new IntTag("primary", 0),
			new IntTag("secondary", 0),
 			new IntTag("x", $block->x),
 			new IntTag("y", $block->y),
 			new IntTag("z", $block->z)
 		]);
 		$pot = Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);
 		return true;
 	}
	
	public function onActivate(Item $item, Player $player = null){
 		if($player instanceof Player){
 			$top = $this->getSide(1);
 			if($top->isTransparent() !== true){
				return true;
 			}
 
			$t = $this->getLevel()->getTile($this);
 			$beacon = null;
 			if($t instanceof TileBeacon){
 				$beacon = $t;
 			}else{
 				$nbt = new CompoundTag("", [
 					new StringTag("id", Tile::BEACON),
 					new ByteTag("isMovable", (bool) false),
 					new IntTag("primary", 0),
 					new IntTag("secondary", 0),
 					new IntTag("x", $this->x),
 					new IntTag("y", $this->y),
 					new IntTag("z", $this->z)
 				]);
 				Tile::createTile(Tile::BEACON, $this->getLevel(), $nbt);
 			}
			
			if($player->isCreative() and $player->getServer()->limitedCreative){
				return true;
			}
 				$player->addWindow($beacon->getInventory());
 		}
 
 		return true;
 	}
	
	public function onBreak(Item $item){
		$this->getLevel()->setBlock($this, new Air(), true, true);
		return true;
	}
 }