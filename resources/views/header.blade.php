<?php
use Modules\Oratorio\Entities\Oratorio;
if(!Auth::guest()){
	$seg = Menu::get('SegrestaNavBar');
	$seg->add("Help", "https://doc.segresta.it")
	->prepend("<i class='fa fa-life-ring' aria-hidden='true'></i> ")
	->data('permissions', ['usermodule', 'all'])->data('order', 70);

	//filtro il menu popolato dai vari moduli in base al ruolo
	$seg = Menu::get('SegrestaNavBar');
	$seg->filter(function($item){
		return ($item->data('permissions') == null || Auth::user()->can($item->data('permissions'))) ? : false;
	});
	$seg->sortBy('order');
}
?>

<nav class="navbar navbar-expand-md navbar-light navbar-laravel">

	<div class="container">
		<!-- <div class="navbar-header"> -->
		<a class="navbar-brand" href="{{ url('/home') }}">
			<?php
			if(Session::get('session_oratorio') != null){
				$oratorio = Oratorio::where('id', Session::get('session_oratorio'))->first();
				if($oratorio->logo != ''){
					echo "<img src='".url(Storage::url('public/'.$oratorio->logo))."' height='60px' ><br>";
				}else{
					echo "<img src='".asset('/assets/logo.png')."' height='60px'><br>";
				}
			}else{
				echo "<img src='".asset('/assets/logo.png')."' height='60px'><br>";
			}
			?>

		</a>

		<!-- </div> -->
		<button class="navbar-toggler navbar-toggler-left" type="button" data-toggle="collapse"
		data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
		aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarCollapse" style="background-color: white;">
		<ul class="navbar-nav mr-auto">
			@if (!Auth::guest())
			@include('custom-menu-items', array('items' => $seg->roots()))
			@endif
		</ul>

		<ul class="navbar-nav ml-auto">
			<!-- Authentication Links -->
			@guest
			<li class="nav-item">
				<a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
			</li>
			<li class="nav-item">
				@if (Route::has('register'))
				<a class="nav-link" href="{{ route('register') }}">Registrati</a>
				@endif
			</li>

			@else
			<li class="nav-item dropdown">
				<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
					<?php
					if(Session::get('session_oratorio')!=null){
						$user = Auth::user();
						if($user->photo==''){
							if($user->sesso=="M"){
								echo "<img src='".url("boy.png")."'>";
							}else if($user->sesso=="F"){
								echo "<img src='".url("girl.png")."'>";
							}

						}else{
							echo "<img src='".url(Storage::url('public/'.$user->photo))."' width=48px/>";
						}
					}

					?>
					{{ Auth::user()->full_name }} <span class="caret"></span>
				</a>

				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
					<a class="dropdown-item" href="{{ url('profile/show') }}"><i class="fas fa-user-circle"></i> Profilo</a>
					@if(Auth::user()->can('view-iscrizioni'))
					<a class="dropdown-item" href="{{ route('iscrizioni.index') }}"><i class="fa fa-flag"></i> Le tue iscrizioni</a>
					@endif
					@if(Module::has('telegram'))
					<a class="dropdown-item" href="{{ route('telegram.index') }}"><i class="fab fa-telegram-plane"></i> Telegram</a>
					@endif
					@if(Auth::user()->hasRole('owner'))
					<a class="dropdown-item" href="{{route('oratorio.showall')}}"><i class="fas fa-cogs"></i> Gestione oratori</a>
					@endif

					<a class="dropdown-item" href="{{ route('logout') }}"
					onclick="event.preventDefault();
					document.getElementById('logout-form').submit();">
					<i class="fas fa-sign-out-alt"></i> Logout
				</a>

				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>

			</div>
		</li>
		@endguest
	</ul>
</div>
</div>
</nav>
