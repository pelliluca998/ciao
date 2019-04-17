<?php
use Modules\User\Entities\User;
use App\Role;
use App\Permission;
use Modules\Event\Entities\Week;
use App\CampoWeek;
use App\Campo;
use App\SpecSubscription;
use App\Classe;

echo Form::open(['route' => 'week.savecampos']);
echo "<table class='testgrid' id='showcampos'>";
echo "<thead><tr>";
echo "<th>Campo</th><th>Mostrare?</th><th>Cancella</th>";
echo "</tr></thead>";


$campos = (new CampoWeek)->select('campo_weeks.id_campo', 'campos.label', 'campo_weeks.id', 'campo_weeks.value')->leftJoin('campos', 'campos.id', '=', 'campo_weeks.id_campo')->where('id_week', $id_week)->get();
$t=0;
foreach($campos as $campo){
	echo "<tr>";
	echo "<td>".$campo->label."</td>";
	$checked="";
	if($campo->value==1){
		$checked="checked";
	}
	echo "<td>";
	echo "<input name='id_week[$t]' type='hidden' value='$id_week'/>";
	echo "<input name='id_campo[$t]' type='hidden' value='".$campo->id_campo."'/>";
	echo "<input name='valore[$t]' type='hidden' value='0'/>";
	echo "<input name='valore[$t]' type='checkbox' value='1' $checked />";
	echo "<input name='id_campoweek[$t]' type='hidden' value='".$campo->id."'/>";
	echo "</td>";
	echo "<td><a href='".url('admin/campoweeks', $campo->id)."/destroy'><i class='fa fa-trash fa-2x' aria-hidden='true'></i></a></td>";
	echo "</tr>";
	$t++;
}
echo "</table><br>";
echo "<input id='contatore' type='hidden' value='$t'/>";
	
	
	

echo Form::submit("Salva", ['class' => 'btn btn-primary form-control', 'style' => 'width: 50%']);
echo "<i onclick='showcampi_add($id_week);' class='btn btn-primary' style='width: 50%'><i class='fa fa-plus' aria-hidden='true'></i> Aggiungi campo</i>";
?>
{!! Form::close() !!}
