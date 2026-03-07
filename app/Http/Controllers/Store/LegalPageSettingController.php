<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;

class LegalPageSettingController extends Controller
{
    public function index()
    {
        $pages = LegalPage::all();
        return view('settings.legal_pages.index', compact('pages'));
    }

    public function create()
    {
        return view('settings.legal_pages.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:legal_pages,slug',
            'content' => 'nullable|string',
        ]);

        LegalPage::create($data);

        return redirect()->route('settings.legal.index')->with('success', 'Legal page created successfully.');
    }

    public function edit($id)
    {
        $page = LegalPage::findOrFail($id);
        return view('settings.legal_pages.form', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = LegalPage::findOrFail($id);
        
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:legal_pages,slug,'.$id,
            'content' => 'nullable|string',
        ]);

        $page->update($data);

        return redirect()->route('settings.legal.index')->with('success', 'Legal page updated successfully.');
    }

    public function destroy($id)
    {
        $page = LegalPage::findOrFail($id);
        $page->delete();

        return redirect()->route('settings.legal.index')->with('success', 'Legal page deleted successfully.');
    }
}
