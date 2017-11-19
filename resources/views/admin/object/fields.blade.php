@php
	if (!isset($object)) {
		$object = new stdClass();
		$object->name = old('name');
		$object->slug = old('slug');
		$object->text = old('text');
		$object->excerpt = old('excerpt');
		$object->author = old('author');
		$object->template = old('template');
		$object->status = old('status');
		$object->comments = old('comments');
	}

	if (isset($object->_old_slug)) {
		$object->slug = $object->_old_slug;
	}

	echo "<pre>";
	var_dump($object);
	echo "</pre>";
@endphp

<label for="object-object-name">{{ __('general.name') }}</label>
<input type="text" id="object-object-name" class="autofocus" name="name" placeholder="name" value="{{ $object->name }}" autofocus>

<label for="object-object-slug">{{ __('general.slug') }}</label>
<input type="text" id="object-object-slug" name="slug" placeholder="slug" value="{{ $object->slug }}">

<label for="object-object-text">{{ __('general.text') }}</label>
<textarea id="object-object-text" name="text" placeholder="text">{{ $object->text }}</textarea>

<label for="object-object-excerpt">{{ __('general.excerpt') }}</label>
<textarea id="object-object-excerpt" name="excerpt" placeholder="excerpt">{{ $object->excerpt }}</textarea>

@foreach ($taxonomies as $taxonomy)
	<p><strong>{{ $taxonomy->name }}</strong></p>
	<ul>
		@foreach ($terms as $term)
			@if ($term['taxonomy'] == $taxonomy->id)
				<li>
					<label for="term-{{ $term['id'] }}">{{ $term['name'] }}</label>
					<input id="term-{{ $term['id'] }}" type="checkbox" name="terms[]" value="{{ $term['id'] }}" {{ $term['id'] == $term->parent ? 'selected' : null }}>

					@if ($term['hasChildren'])
						<ul>
							@foreach ($term['children'] as $child)
								<li>
									<label for="term-{{ $child['id'] }}">{{ $child['name'] }}</label>
									<input id="term-{{ $child['id'] }}" type="checkbox" name="terms[]" value="{{ $child['id'] }}">
								</li>
							@endforeach
						</ul>
					@endif
				</li>
			@endif
		@endforeach
	</ul>
@endforeach


<label for="object-object-author">{{ __('general.author') }}</label>
<select id="object-object-author" name="author">
	<option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
</select>

{{-- <input type="text" id="object-object-author" name="author" placeholder="author" value="{{ $object->author }}"> --}}

<label for="object-object-template">{{ __('general.template') }}</label>
<input type="text" id="object-object-template" name="template" placeholder="template" value="{{ $object->template }}">

{{-- <label for="object-object-status">{{ __('general.status') }}</label>
<input type="text" id="object-object-status" name="status" placeholder="status" value="{{ $object->status }}"> --}}

<p><strong>Statuses</strong></p>
<select name="status">
	@foreach ($statuses as $status)
		<option value="{{ $status['id'] }}">{{ $status['name'] }}</option>

		{{-- {{ $status['id'] == $object->status ? 'selected' : null }} --}}

	@endforeach
</select>

<label for="object-object-comments">{{ __('general.comments') }}</label>
<input type="checkbox" id="object-object-comments" name="comments" placeholder="comments" {{ $object->comments ? 'checked' : null }}>