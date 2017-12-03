<nav id="nav-main">
	<ul>
		<li>
			<a href="{{ route('home') }}">
				<img id="nav-logo" src="/images/peak/logo/white.svg" alt="{{ config('app.name', 'PEAK') }} logo">
				<span>{{ config('app.name', 'PEAK') }}</span>
			</a>
		</li>
		@if (Auth::check())
			<li><a href="{{ route('admin.dashboard') }}">admin</a></li>
			<li>
				<a href="{{ route('profile') }}">
					<img src="{{ $profile->image->url }}" alt="{{ $profile->image->alt }}" width="32px">
					<span>{{ $profile->name_display }}</span>
				</a>
				<ul>
					<li>
						<a id="admin-logout-action" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
							Logout
						</a>
						<form id="admin-logout-form" action="{{ route('logout') }}" method="POST">
							{{ csrf_field() }}
						</form>
					</li>
				</ul>
			</li>
		@else
			<li><a href="{{ route('login') }}">Login</a></li>
			<li><a href="{{ route('register') }}">Register</a></li>
		@endif
	</ul>
</nav>