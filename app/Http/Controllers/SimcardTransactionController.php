<?php
namespace App\Http\Controllers;
use App\Models\SimcardTransaction;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimcardTransactionController extends Controller
{
    // Show unified SIM transaction form
    public function create()
    {
        $projects = Project::select('id', 'project_id', 'project_name', 'entity')
                           ->orderBy('project_id')
                           ->get();

        // Get available SIMs for assignment and assigned SIMs for return
        $availableSimcards = SimcardTransaction::getAvailableSimcards();
        $assignedSimcards = SimcardTransaction::getAssignedSimcards();

        return view('simcards.form', compact('projects', 'availableSimcards', 'assignedSimcards'));
    }

    // Store assignment or return
    public function store(Request $request)
    {
        $request->validate([
            'transaction_type' => 'required|in:assign,return',
            'simcard_number'   => 'required|max:100',
            'project_id'       => 'required_if:transaction_type,assign|exists:projects,id',
            'mrc'              => 'nullable|numeric',
            'issue_date'       => 'required_if:transaction_type,assign|date',
            'return_date'      => 'required_if:transaction_type,return|date|after_or_equal:issue_date',
            'pm_dc'            => 'nullable|max:100',
        ]);

        DB::beginTransaction();
        try {
            if ($request->transaction_type === 'assign') {
                $project = Project::findOrFail($request->project_id);

                SimcardTransaction::create([
                    'transaction_type' => 'assign',
                    'simcard_number'   => $request->simcard_number,
                    'project_id'       => $project->id,
                    'project_name'     => $project->project_name,
                    'entity'           => $project->entity,
                    'mrc'              => $request->mrc,
                    'issue_date'       => $request->issue_date,
                    'pm_dc'            => $request->pm_dc,
                ]);

                $message = 'SIM card assigned successfully!';
            } else {
                // For return
                $assignment = SimcardTransaction::where('simcard_number', $request->simcard_number)
                    ->where('transaction_type', 'assign')
                    ->whereNull('return_date')
                    ->firstOrFail();

                $assignment->update(['return_date' => $request->return_date]);

                SimcardTransaction::create([
                    'transaction_type' => 'return',
                    'simcard_number'   => $request->simcard_number,
                    'project_id'       => $assignment->project_id,
                    'project_name'     => $assignment->project_name,
                    'entity'           => $assignment->entity,
                    'mrc'              => $assignment->mrc,
                    'issue_date'       => $assignment->issue_date,
                    'return_date'      => $request->return_date,
                    'pm_dc'            => $assignment->pm_dc,
                ]);

                $message = 'SIM card returned successfully!';
            }

            DB::commit();
            return redirect()->route('simcards.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }

    // Get SIM details for return form
    public function getSimDetails($simcardNumber)
    {
        $assignment = SimcardTransaction::where('simcard_number', $simcardNumber)
            ->where('transaction_type', 'assign')
            ->whereNull('return_date')
            ->first();

        if (!$assignment) {
            return response()->json(['error' => 'SIM not found or already returned'], 404);
        }

        return response()->json([
            'project_id'   => $assignment->project_id,
            'project_name' => $assignment->project_name,
            'entity'       => $assignment->entity,
            'mrc'          => $assignment->mrc,
            'issue_date'   => $assignment->issue_date, // keep as string
            'pm_dc'        => $assignment->pm_dc,
        ]);
    }

    // Index page - show all transactions
    public function index()
    {
        $transactions = SimcardTransaction::orderBy('created_at', 'desc')->paginate(20);

        return view('simcards.index', compact('transactions'));
    }
}
