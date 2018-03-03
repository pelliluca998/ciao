<?php
use Modules\User\Entities\User;
use App\Role;
use Modules\User\Entities\Group;
use App\Permission;
use App\TypeSelect;
?>

<?php
echo Form::open(['route' => 'typeselect.save']);
$specs = (new TypeSelect)
	->where('id_type', $id_type)->orderBy('ordine', 'asc')->get();
$i=0;
echo "<table class='testgrid' id='showoptions'>";
echo "<tr><th>Opzione</th><th>Ordine</th><th>Del</th></tr>";
foreach($specs as $spec){
	echo "<tr>";
echo "<td><input name='id_option[$i]' type='hidden' value='".$spec->id."' /><input name='id_type[$i]' type='hidden' value='".$id_type."' /><input name='option[$i]' type='text' value='".$spec->option."' style='width: 100%'/></td>";
	$icon = "<a href='".url('admin/typeselect', [$spec->id])."/destroy'><i class='fa fa-trash fa-2x' aria-hidden='true'></i></a>";
	echo "<td><input type='number' min='0' name='ordine[$i]' value='".$spec->ordine."'</td>";
	echo "<td>$icon</td>";
	echo "</tr>";
	$i++;
	
}
echo "</table><br>";
?><input id='contatore_e' type='hidden' value='<?php echo $i; ?>' />
{!! Form::submit('Salva', ['class' => 'btn btn-primary form-control', 'style' => 'width: 45%']) !!}
<i onclick='typeselect_add(<?php echo $id_type; ?>);' class='btn btn-primary' style='width: 45%'><i class='fa fa-plus' aria-hidden='true'></i> Aggiungi opzione</i>
		{!! Form::close() !!}
