<?php

namespace Modules\Oratorio\Entities;

use Illuminate\Database\Eloquent\Model;
use Session;

class Type extends Model
{
	protected $fillable = ['label', 'description', 'id_oratorio'];
	
	public static function getTypesBase(){
		$type_base = array();
		//text type
		$type = new Type();
		$type->id = -1;
		$type->label = "Testo";
		$type->description = "Testo";
		array_push($type_base, $type);
		//checkbox type
		$type = new Type();
		$type->id = -2;
		$type->label = "Checkbox";
		$type->description = "Checkbox";
		array_push($type_base, $type);
		//number type
		$type = new Type();
		$type->id = -3;
		$type->label = "Numero";
		$type->description = "Numero";
		array_push($type_base, $type);
		//Group type
		$type = new Type();
		$type->id = -4;
		$type->label = "Gruppo";
		$type->description = "Gruppo";
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
}
