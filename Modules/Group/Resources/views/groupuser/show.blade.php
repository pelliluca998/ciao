<?php
use App\User;
use App\Role;
use App\Group;
use App\Permission;
use Nayjest\Grids\Components\Base\RenderableRegistry;
use Nayjest\Grids\Components\ColumnHeadersRow;
use Nayjest\Grids\Components\ColumnsHider;
use Nayjest\Grids\Components\CsvExport;
use Nayjest\Grids\Components\ExcelExport;
use Nayjest\Grids\Components\Filters\DateRangePicker;
use Nayjest\Grids\Components\FiltersRow;
use Nayjest\Grids\Components\HtmlTag;
use Nayjest\Grids\Components\Laravel5\Pager;
use Nayjest\Grids\Components\OneCellRow;
use Nayjest\Grids\Components\RecordsPerPage;
use Nayjest\Grids\Components\RenderFunc;
use Nayjest\Grids\Components\ShowingRecords;
use Nayjest\Grids\Components\TFoot;
use Nayjest\Grids\Components\THead;
use Nayjest\Grids\Components\TotalsRow;
use Nayjest\Grids\DbalDataProvider;
use Nayjest\Grids\EloquentDataProvider;
use Nayjest\Grids\FieldConfig;
use Nayjest\Grids\IdFieldConfig;
use Nayjest\Grids\FilterConfig;
use Nayjest\Grids\Grid;
use Nayjest\Grids\GridConfig;
use Nayjest\Grids\ObjectDataRow;
?>

<?php
//echo Form::open(['route' => 'specsubscription.save']);
//carico tutte le settimane
$users = (new User)->select('users.id', 'name', 'cognome', 'group_users.id as id_assoc')
	->leftJoin('group_users', 'users.id', '=', 'group_users.id_user')
	->where('group_users.id_group', $id_group)->orderBy('cognome', 'asc')->get();
$i=1;
echo "<table class='testgrid'>";
echo "<tr><th>Utente</th><th>Del</th></tr>";
foreach($users as $user){
	echo "<tr>";
	echo "<td>".$user->cognome." ".$user->name."</td>";
	$icon = "<a href='".url('admin/groupusers', [$user->id_assoc])."/destroy'><i class='fa fa-trash fa-2x' aria-hidden='true'></i></a>";
	echo "<td>$icon</td>";
	echo "</tr>";	
	
}
echo "</table>";
//echo Form::submit('Salva', ['class' => 'btn btn-primary form-control']);
?>

