<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Election;
use App\Models\Portfolio;
use App\Models\User;
use App\Models\ElectionApprovalAdmin;
use Session;
use Auth;

class ElectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_tenant = Auth::user()->tenant_id;
        $elections = Election::where('tenant_id',$user_tenant)->get();

        // Update status for each election based on current time
        foreach ($elections as $election) {
            $election->updateStatus();
        }

        return view('tenant.elections.index', compact('elections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $user = Auth::user();
        $clientAdmins = User::where('tenant_id', $user->tenant->id)
                           ->where('role', 'client_admin')
                           ->get();
        return view('tenant.elections.create', compact('clientAdmins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'election_name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'allowed_votes' => 'required|integer|min:1',
            'content' => 'required|string',
            'approval_admins' => 'required|array|min:1',
            'approval_admins.*' => 'exists:users,id',
        ]);

        $user = Auth::user();

        $election = Election::create([
            'tenant_id' => $user->tenant->id,
            'name' => $request->election_name,
            'description' => $request->content,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'allowed_votes' => $request->allowed_votes,
            'user_id' => Auth::user()->id
        ]);

        // Store approval admins
        if ($request->has('approval_admins')) {
            foreach ($request->approval_admins as $adminId) {
                ElectionApprovalAdmin::create([
                    'election_id' => $election->id,
                    'user_id' => $adminId
                ]);
            }
        }

        if($request->portfolio_name)
        {
            foreach ($request->portfolio_name as $key => $portfolio) 
            {
                // code...
                Portfolio::create([
                    'tenant_id' => $user->tenant->id,
                    'election_id' => $election->id,
                    'name' => $portfolio['portfolio'],
                    'max_votes' => 1 //default is 1, unless change at portfolio screen
                ]);
            }
        }

        Session::flash('success', 'Election created successfully!');
        return redirect()->route('elections.index');
    }

    public function results($id)
    {
        $election = Election::with(['categories.candidates'])->findOrFail($id);

        // $results = Vote::where('election_id', $id)
        //     ->selectRaw('candidate_id, COUNT(*) as total')
        //     ->groupBy('candidate_id')
        //     ->pluck('total','candidate_id');


        $results = Vote::whereHas('ballot', function ($q) use ($id) {
            $q->where('election_id', $id);
        })
        ->selectRaw('candidate_id, COUNT(*) as total')
        ->groupBy('candidate_id')
        ->pluck('total', 'candidate_id');

        return view('elections.results', compact('election','results'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //You never show live results during active voting unless required.
        if ($election->status !== 'closed') {
            abort(403);
        }
    }

    public function exportPdf($id)
    {
        $election = Election::with('candidates.votes')->findOrFail($id);

        $pdf = Pdf::loadView('admin.pdf.results', compact('election'));

        return $pdf->download('election-results.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //Cannot edit and on-going election
        //Elections are either Ready To Start; In Progress; Completed
        $election = Election::findOrFail($id);
        $user = Auth::user();
        $clientAdmins = User::where('tenant_id', $user->tenant->id)
                           ->where('role', 'client_admin')
                           ->get();
        
        // Get currently selected approval admins for this election
        $selectedApprovalAdmins = ElectionApprovalAdmin::where('election_id', $election->id)
                                                        ->pluck('user_id')
                                                        ->toArray();
        
        return view('tenant.elections.edit', compact('election', 'clientAdmins', 'selectedApprovalAdmins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'election_name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'allowed_votes' => 'required|integer|min:1',
            'content' => 'required|string',
            'approval_admins' => 'required|array|min:1',
            'approval_admins.*' => 'exists:users,id',
        ]);

        $election = Election::findOrFail($id);

        $election->update([
            'name' => $request->election_name,
            'description' => $request->content,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'allowed_votes' => $request->allowed_votes,
        ]);

        // Update approval admins - remove existing and add new ones
        ElectionApprovalAdmin::where('election_id', $election->id)->delete();
        
        if ($request->has('approval_admins')) {
            foreach ($request->approval_admins as $adminId) {
                ElectionApprovalAdmin::create([
                    'election_id' => $election->id,
                    'user_id' => $adminId
                ]);
            }
        }

        Session::flash('success', 'Election updated successfully!');
        return redirect()->route('elections.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
