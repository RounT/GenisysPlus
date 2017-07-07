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

 
namespace pocketmine\level\generator\normal\object;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\Object;
use pocketmine\utils\Random;

class SugarCaneStack extends Object{
	
	/** @var Random */
	private $random;
	private $baseHeight = 2;
	private $randomHeight = 4;
	private $totalHeight;
	
	public function __construct(Random $random){
		$this->random = $random;
		$this->randomize();
	}
	
	public function randomize(){
		$this->totalHeight = $this->baseHeight + $this->random->nextBoundedInt($this->randomHeight);
	}
	
	private function isWater(int $id) : bool{
		if($id == Block::WATER or $id == Block::STILL_WATER){
			return true;
		}
		return false;
	}
	
	public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z) : bool{
		$below = $level->getBlockIdAt($x, $y - 1, $z);
		if($level->getBlockIdAt($x, $y, $z) == Block::AIR and
			($below == Block::DIRT or $below == Block::GRASS or $below == Block::SAND) and (
				$this->isWater($level->getBlockIdAt($x - 1, $y - 1 , $z)) or
				$this->isWater($level->getBlockIdAt($x + 1, $y - 1, $z)) or
				$this->isWater($level->getBlockIdAt($x, $y - 1, $z - 1)) or
				$this->isWater($level->getBlockIdAt($x, $y - 1, $z + 1))
			)
		){
			return true;
		}
		return false;
	}
	
	public function placeObject(ChunkManager $level, int $x, int $y, int $z){
		for($yy = 0; $yy < $this->totalHeight; $yy++){
			if($level->getBlockIdAt($x, $y + $yy, $z) != Block::AIR){
				return;
			}
			$level->setBlockIdAt($x, $y + $yy, $z, Block::SUGARCANE_BLOCK);
		}
	}
}