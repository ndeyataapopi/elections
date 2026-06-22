<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Election;
use App\Models\Portfolio;
use Session;
use Auth;

class PortfolioController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user_tenant = Auth::user()->tenant_id;
        $elections = Election::where('tenant_id',$user_tenant)->get();
        return view('tenant.portfolios.index', compact('elections'));
    }

    public function view($id)
    {
        //
        $election = Election::findOrFail($id);
        return view('tenant.portfolios.view', compact('election'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
        $portfolio = Portfolio::findOrFail($id);
        return view('tenant.portfolios.edit', compact('portfolio'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'max_votes' => 'required|integer|min:1',
        ]);

        $portfolio = Portfolio::findOrFail($id);
        $portfolio->update([
            'name' => $request->name,
            'max_votes' => $request->max_votes,
        ]);

        Session::flash('success', 'Portfolio updated successfully!');
        return redirect()->route('portfolios.view', $portfolio->election_id);
    }
}
