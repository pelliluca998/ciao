<?php

namespace Modules\Oratorio\Entities;

use Illuminate\Database\Eloquent\Model;
use Session;

class Type extends Model
{
	protected $fillable = ['label', 'description', 'id_oratorio'];

	const TEXT_TYPE = -1;
  const BOOL_TYPE = -2;
  const NUMBER_TYPE = -3;
  const DATE_TYPE = -4;

	public static function getTypesBase(){
		$type_base = array();
		//text type
		$type = new Type();
		$type->id = self::TEXT_TYPE;
		$type->label = "Testo";
		//$type->description = "Testo";
		array_push($type_base, $type);
		//checkbox type
		$type = new Type();
		$type->id = self::BOOL_TYPE;
		$type->label = "Checkbox";
		//$type->description = "Checkbox";
		array_push($type_base, $type);
		//number type
		$type = new Type();
		$type->id = self::NUMBER_TYPE;
		$type->label = "Numero";
		//$type->description = "Numero";
		array_push($type_base, $type);
		$type = new Type();
		$type->id = self::DATE_TYPE;
		$type->label = "Data";
		array_push($type_base, $type);
		return $type_base;
	}

	public static function getTypes(){
		$types = Type::select('label', 'id')
		->where('id_oratorio', Session::get('session_oratorio'))
		->get();
		$type_base = Type::getTypesBase();
		foreach($type_base as $base){
			$types->prepend($base);
		}
		return $types->pluck('label', 'id');
	}

	public static function getTypeLabel($key){
		if($key < 0){
			foreach(self::getTypesBase() as $type){
				if($type->id == $key)
				return $type->label;
			}
		}

		$type = Type::find($key);
		return $type != null? $type->label : "";
	}
}
