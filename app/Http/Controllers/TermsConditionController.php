<?php

namespace App\Http\Controllers;

use App\Models\TermsCondition;
use Illuminate\Http\Request;

class TermsConditionController extends Controller
{
    public function index()
    {
        $buyTerms  = TermsCondition::where('type','buy')->latest()->get();
        $sellTerms = TermsCondition::where('type','sell')->latest()->get();
        return view('terms.index', compact('buyTerms','sellTerms'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'      => 'required|in:buy,sell',
            'title'     => 'required|string|max:150',
            'content'   => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        // Deactivate others of same type if this one is active
        if ($data['is_active']) {
            TermsCondition::where('type', $data['type'])->update(['is_active' => false]);
        }

        TermsCondition::create($data);
        return back()->with('success', 'Terms saved!');
    }

    public function update(Request $request, TermsCondition $term)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:150',
            'content'   => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', false);

        if ($data['is_active']) {
            TermsCondition::where('type', $term->type)->where('id','!=',$term->id)->update(['is_active'=>false]);
        }

        $term->update($data);
        return back()->with('success', 'Terms updated!');
    }

    public function destroy(TermsCondition $term)
    {
        $term->delete();
        return back()->with('success', 'Terms deleted.');
    }

    // AJAX — return active terms for a type
    public function get(string $type)
    {
        $terms = TermsCondition::activeFor($type);
        return response()->json(['content' => $terms?->content ?? '', 'title' => $terms?->title ?? '']);
    }
}
