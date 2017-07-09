<?php

namespace App\Http\Controllers\Admin\Object;

use Session;
use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\Validator;

use \App\Model\Object\Type;
use \App\Model\Object\Taxonomy;
use \App\Model\Object\Term;

class TermController extends Controller
{
    public function index(Type $type, Taxonomy $taxonomy)
    {
        $taxonomy = Taxonomy::getSingle($type, $taxonomy);
        $terms = Term::where('taxonomy', $taxonomy->id)->get();
        $parents = Term::where(['taxonomy' => $taxonomy->id, 'parent' => null])->get();

        foreach ($parents as $parent) {
            $parent->getChildrenRecursively();
        }

        return view('admin.builder.object.term.index', compact('type', 'taxonomy', 'parents', 'terms'));
    }

    public function create(Type $type, Taxonomy $taxonomy)
    {
        $taxonomy = Taxonomy::getSingle($type, $taxonomy);
        $terms = Term::where('taxonomy', $taxonomy->id)->get();
        return view('admin.builder.object.term.create', compact('type', 'taxonomy', 'terms'));
    }

    public function store(Type $type, Taxonomy $taxonomy, Request $request)
    {
        $taxonomy = Taxonomy::getSingle($type, $taxonomy);
        $request->merge(['taxonomy' => $taxonomy->id]);

        if (!$request->slug && $request->name) $request->merge(['slug' => str_slug($request->name, '-')]);

        $this->validate($request, [
            'name'          => 'required|string|min:2',
            'slug'          => 'required|unique_with:object_terms,taxonomy|string|min:2',
            'parent'        => 'integer|nullable',
            'template'      => 'integer|nullable',
        ]);

        Term::create(request([
            'name',
            'slug',
            'parent',
            'template',
            'taxonomy'
        ]));

        Session::flash('alert-success', __('validation.succeeded.create', ['name' => $request->name]));
        return back();
    }

    public function edit(Type $type, Taxonomy $taxonomy, Term $term)
    {
        $taxonomy = Taxonomy::getSingle($type, $taxonomy);
        $term = Term::getSingle($type, $taxonomy, $term);
        $terms = Term::where('taxonomy', $taxonomy->id)->get();

        if (session('_old_input') !== null) {
            $slug = $term->slug; // Keep the original slug to prevent url issues
            $term = json_decode(json_encode(session('_old_input')), false); // Fill object with old input values
            $term->_old_slug = $term->slug;
            $term->slug = $slug;
        }

        return view('admin.builder.object.term.edit', compact('type', 'taxonomy', 'term', 'terms'));
    }

    public function update(Type $type, Taxonomy $taxonomy, Request $request, Term $term)
    {
        $taxonomy = Taxonomy::getSingle($type, $taxonomy);
        $term = Term::getSingle($type, $taxonomy, $term);

        if (!$request->slug && $request->name) $request->merge(['slug' => str_slug($request->name, '-')]);
        
        $request->merge(['taxonomy' => $taxonomy->id]);

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|min:2',
            'slug'          => 'required|unique_with:object_terms,taxonomy,'.$term->id.'|string|min:2',
            'parent'        => 'integer|nullable',
            'template'      => 'integer|nullable',
        ]);

        if ($validator->fails()) {
            Session::flash('alert-danger', __('validation.failed.update', ['name' => $term->name]));
            return redirect()->route('object.term.edit', [$type->slug, $taxonomy->slug, $term->slug])->withErrors($validator)->withInput();
        }

        $slug_changed = ($term->slug == $request->slug) ? false : true;

        $term->name     = $request->name;
        $term->slug     = $request->slug;
        $term->parent   = $request->parent;
        $term->template = $request->template;

        $term->save();

        Session::flash('alert-success', __('validation.succeeded.update', ['name' => $term->name]));

        if ($slug_changed) {
            return redirect()->route('object.term.edit', [$type->slug, $taxonomy->slug, $term->slug]);
        }

        return back();
    }

    public function destroy(Type $type, Taxonomy $taxonomy, Term $term)
    {
        $taxonomy = Taxonomy::getSingle($type, $taxonomy);
        $term = Term::getSingle($type, $taxonomy, $term);

        $term->delete();

        Session::flash('alert-success', __('validation.succeeded.delete', ['name' => $term->name]));
        return back();
    }
}
