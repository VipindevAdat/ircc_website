<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $landingPages = LandingPage::orderBy('year', 'desc')->get();
        return view('admin.landing-pages.index', compact('landingPages'));
    }

    public function create()
    {
        return view('admin.landing-pages.form', ['landingPage' => new LandingPage()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|unique:landing_pages,year',
            'title' => 'nullable|string|max:255',
        ]);
        LandingPage::create($request->only(['year', 'title', 'is_active']));
        return redirect()->route('admin.landing-pages.index')->with('success_message', 'Landing page created successfully');
    }

    public function edit(LandingPage $landingPage)
    {
        return view('admin.landing-pages.form', compact('landingPage'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $request->validate([
            'year' => 'required|integer|unique:landing_pages,year,' . $landingPage->id,
            'title' => 'nullable|string|max:255',
        ]);
        $landingPage->update($request->only(['year', 'title', 'is_active']));
        return redirect()->route('admin.landing-pages.index')->with('success_message', 'Landing page updated successfully');
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();
        return response()->json(['success' => 'Landing page deleted successfully'], 200);
    }
}