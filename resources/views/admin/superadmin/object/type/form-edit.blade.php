<form method="POST" action="{{ route('superadmin.object.type.update', $type->slug) }}">
	{{ method_field('PATCH') }}
	{{ csrf_field() }}

	@include('admin.superadmin.object.type.fields')

	<input type="submit">
</form>