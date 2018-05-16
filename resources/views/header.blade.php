<?php
?>

<nav class="navbar navbar-default">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle Navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="{{ url('/home') }}"><img src="{{url('logo_segresta.png')}}" width='70px' ></a>
		</div>

		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				@if (!Auth::guest())
				<?php
				//Menu::make('MyNavBar', function($menu){});
				$menuList = Menu::get('SegrestaNavBar');
				$menuList->add("Help", "http://doc.segresta.it")
				->prepend("<i class='fa fa-life-ring' aria-hidden='true'></i> ")
				->data('permissions', ['usermodule', 'all'])->data('order', 70);

				//filtro il menu popolato dai vari moduli in base al ruolo
				$seg = Menu::get('SegrestaNavBar');
				$seg->filter(function($item){
					return Auth::user()->can($item->data('permissions')) ? : false;
				});
				$seg->sortBy('order');

				?>
				@include('custom-menu-items', array('items' => $SegrestaNavBar->roots()))
				@endif



			</ul>

			<ul class="nav navbar-nav navbar-right">
				@if (Auth::guest())
				<li class="{{ (Request::is('login') ? 'active' : '') }}"><a href="{{ url('login') }}"><i
					class="fa fa-sign-in"></i> Login</a></li>
					<li class="{{ (Request::is('register') ? 'active' : '') }}"><a
						href="{{ url('register') }}">Registrati</a></li>
						@else
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
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
								{{ Auth::user()->name }}
								<i class="fa fa-caret-down"></i>
							</a>
							<ul class="dropdown-menu" role="menu">
								@if(Auth::check())
								<li><a href="{{url('profile/show')}}"><i class="fa fa-user" aria-hidden='true'></i> Profilo</a></li>
								<li><a href="{{route('usersubscriptions.show')}}"><i class="fa fa-flag" aria-hidden='true'></i> Le tue iscrizioni</a></li>
								<li><a href="{{route('oratorio.affiliazione')}}"><i class="fa fa-cubes" aria-hidden='true'></i> Affiliazione oratorio</a></li>
								@if(Module::find('telegram')!=null)
								<li><a href="{{route('telegram.index')}}"><i class="fab fa-telegram-plane" aria-hidden='true'></i> Telegram</a></li>
								@endif
								<li>
									<a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
										<i class="fas fa-sign-out-alt" aria-hidden='true'></i> Logout
									</a>

									<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
										{{ csrf_field() }}
									</form>
								</li>
								@endif
								@if(Auth::user()->hasRole('owner'))
								<li class="{{ (Request::is('oratorio/showall') ? 'active' : '') }}"><a href="{{route('oratorio.showall')}}"><i class="fas fa-cogs"></i> Gestione oratori</a></li>
								@endif

							</ul>
						</li>
						@endif
					</ul>
				</div>
			</div>
		</nav>
