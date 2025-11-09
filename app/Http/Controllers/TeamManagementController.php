<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\TeamManagement;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TeamManagementController extends Controller
{
    //
    public function time_tracking_index(Request $request)
    {
        $limit = $request->input('limit', 10);
        $userId = Auth::id();

        // Get all tracks for the user
        $tracks = TeamManagement::where('user_id', $userId)->orderBy('date', 'desc')->paginate($limit);

        // Get the user's hourly rate
        $user = User::find($userId);
        $hourlyRate = $user->hourly_rate ?? 0; // Ensure hourly rate is not null

        // Calculate total paid hours and total unpaid amount
        $totalPaidHours = TeamManagement::where('user_id', $userId)->where('paid', true)->sum('hours') ?? 0;
        $totalPaidAmount = $totalPaidHours * $hourlyRate;

        $totalUnpaidAmount = TeamManagement::where('user_id', $userId)->where('paid', false)
            ->sum(DB::raw("hours * {$hourlyRate}")) ?? 0;

        return view('Admin.team_management_time_tracking', compact(
            'tracks',
            'hourlyRate',
            'totalPaidHours',
            'totalPaidAmount',
            'totalUnpaidAmount'
        ));
    }

    //Employee add case
    public function add_time_track(Request $request)
    {
        $userId = Auth::id();

        // $request->validate([
        //     'date' => 'required|date',
        //     'arrival_time' => 'required|date_format:H:i',
        //     'departure_time' => 'required|date_format:H:i|after:arrival_time',
        //     'hours' => 'required|numeric|min:0|max:24',
        //     'notes' => 'nullable|string',
        // ]);

        TeamManagement::updateOrCreate(
            [
                'user_id' => $userId, // Fields to match
                'date' => $request->date
            ],
            [
                'arrival_time' => $request->arrival_time, // Fields to update
                'departure_time' => $request->departure_time,
                'hours' => $request->hours,
                'notes' => $request->notes
            ]
        );

        return response()->json(['success' => true]);
    }
    
    //Employer add case
    public function add_time_track_by_userid(Request $request)
    {
        TeamManagement::updateOrCreate(
            [
                'user_id' => $request->user_id, // Fields to match
                'date' => $request->date
            ],
            [
                'arrival_time' => $request->arrival_time, // Fields to update
                'departure_time' => $request->departure_time,
                'hours' => $request->hours,
                'notes' => $request->notes
            ]
        );

        return response()->json(['success' => true]);
    }

    public function getPageScroll(Request $request)
    {
        if ($request->ajax()) {

            $limit = 10; // Default to 10 if no limit is set
            $tracks = TeamManagement::where('user_id', Auth::id())->orderBy('date', 'desc')->paginate($limit);
            return view('Admin.time_tracking_table', compact('tracks'))->render();
        }
    }

    public function destory_time_track($id)
    {
        $item = TeamManagement::find($id);

        if ($item) {
            $item->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }
    }

    public function edit_time_track(Request $request, $id)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'arrival_time' => 'required|date_format:H:i',
            'departure_time' => 'required|date_format:H:i|after:arrival_time',
            'hours' => 'required|numeric|min:0|max:24',
            'notes' => 'nullable|string',
        ]);

        // Find the entry by ID
        $teamManagement = TeamManagement::findOrFail($id);

        // Update the entry with the validated data
        $teamManagement->update([
            'date' => $validatedData['date'],
            'arrival_time' => $validatedData['arrival_time'],
            'departure_time' => $validatedData['departure_time'],
            'hours' => $validatedData['hours'],
            'notes' => $validatedData['notes'],
        ]);

        return response()->json(['success' => true]);
    }



    public function team_management_index(Request $request)
    {
        $limit = $request->input('limit', 100);

        // Get all employees (excluding archived)
        $employees = User::withTrashed()
                ->where('company_id', session('company_id'))
                ->where('type', 'employee')
                ->whereNull('deleted_at')
                ->paginate($limit);

        $unpaidTracks = TeamManagement::selectRaw('user_id, SUM(hours) as total_hours')
            ->where('paid', false)
            ->where('company_id', session('company_id'))
            ->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            })
            ->groupBy('user_id')
            ->pluck('total_hours', 'user_id');

        $paidTracks = TeamManagement::selectRaw('user_id, SUM(hours) as total_paid_hours')
            ->where('paid', true)
            ->where('company_id', session('company_id'))
            ->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            })
            ->groupBy('user_id')
            ->pluck('total_paid_hours', 'user_id');

        $allTracks = TeamManagement::with('user')
            ->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('date', 'desc')
            ->get();
        $allTrackData = [];

        // Calculate total hours for each user
        foreach ($allTracks as $item) {
            // Skip if user doesn't exist (archived users)
            if (!$item->user) {
                continue;
            }
            
            $allTrackData[$item->user_id][] = [
                'id' => $item->id,
                'date' => $item->date,
                'arrival_time' => $item->arrival_time,
                'departure_time' => $item->departure_time,
                'hours' => $item->hours,
                'paid' => $item->paid,
                'notes' => $item->notes,
                'hourly_rate' => $item->user->hourly_rate // Include hourly rate
            ];
        }
        return view('Admin.team_management_index', compact('employees', 'unpaidTracks', 'paidTracks', 'allTrackData'));
    }

    /**
     * Archive an employee (soft delete)
     */
    public function archiveEmployee(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        
        // Check if user belongs to the same company
        if ($user->company_id !== session('company_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Soft delete the user (archive)
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Employee archived successfully!']);
    }

    /**
     * Restore an archived employee
     */
    public function restoreEmployee(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::withTrashed()->find($request->user_id);
        
        // Check if user belongs to the same company
        if ($user->company_id !== session('company_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Restore the user
        $user->restore();

        return response()->json(['success' => true, 'message' => 'Employee restored successfully!']);
    }

    /**
     * Get archived employees
     */
    public function getArchivedEmployees()
    {
        $archivedEmployees = User::withTrashed()
            ->where('company_id', session('company_id'))
            ->where('type', 'employee')
            ->whereNotNull('deleted_at')
            ->get();

        return response()->json($archivedEmployees);
    }

    public function update_hourly_rate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hourly_rate' => 'required|numeric|min:0',
        ]);

        $user = User::find($request->user_id);
        $user->hourly_rate = $request->hourly_rate;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function pay_user(Request $request)
    {
        $request->validate([
            'track_ids' => 'required|array',
            'track_ids.*' => 'exists:team_time_management,id',
        ]);

        TeamManagement::whereIn('id', $request->track_ids)->update(['paid' => true]);

        return response()->json(['success' => true]);
    }

    public function sendInvite(Request $request)
    {
        // Validate email input
        $request->validate([
            'email' => 'required|email',
        ]);

        $token = base64_encode($request->email . '##' . auth()->user()->company->name . '##' . auth()->user()->company->id . '##' . strtotime('+1 minutes', time()));
        // Check if user exists in the database
        $user = User::where('email', $request->input('email'))
                ->where('type', '!=', 'unauthorized')
                ->first();
        if ($user) {
            // User exists, send the invitation email
            $user->activation_token = $token;
            $user->save();         

            $this->sendInvitationEmail($user, $token);

            return response()->json(['success' => true, 'msg' => 'Invitation sent successfully!']);
        } else {
            // User does not exist
            return response()->json(['success' => false, 'msg' => 'Email address not found.']);
        }
    }

    private function sendInvitationEmail($user, $inviteToken)
    {
        $mail_data = [
            'name' => $user->name,
            'email' => $user->email,
            'subject' => 'You are invited to join the team',
            'company_name' => auth()->user()->company->name,
            'invite_link' => url('https://onhandsolution.com/team/accept_invite/' . $inviteToken)
        ];

        // Send invitation email
        Mail::send('emails.invite', $mail_data, function ($message) use ($mail_data) {
            $message->to($mail_data['email'], $mail_data['name']);
            $message->subject($mail_data['subject']);
        });
    }

    public function acceptInvite($token)
    {
        $decodedToken = base64_decode($token);
        [$email, $companyName, $companyId, $expiry] = explode('##', $decodedToken);
        
        // Find the user by activation token
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid token.');
        }
        $user->activation_token = null;
        $user->type = 'employee';
        $user->company_id = $companyId;
        $user->save();

        Auth::logout();

        return redirect('/login')->with('success', 'You are successfully invited.');
    }

    public function add_time_track_clock_in_and_out(Request $request)
    {
        $userId = Auth::id();
        $action = $request->input('action');
        $time = $request->input('time');
        $formattedTime = \Carbon\Carbon::parse($time)->format('Y-m-d H:i:s'); // Convert to MySQL datetime format
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $formattedTime)->toDateString();

        if ($action === 'clock_in') {
            // Save arrival time
            TeamManagement::updateOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $date
                ],
                [
                    'arrival_time' => $formattedTime,
                    'departure_time' => null,
                    'hours' => 0
                ]
            );
        } elseif ($action === 'clock_out') {
            $timeTrack = TeamManagement::where('user_id', $userId)->where('date', $date)->first();

            
            // Update departure time and calculate hours
            if ($timeTrack && $timeTrack->arrival_time) {
               
                $arrivalDateTimeString = $timeTrack->date . ' ' . $timeTrack->arrival_time;
                $arrival = new \DateTime($arrivalDateTimeString);
                $departure = new \DateTime($formattedTime);
            

                // Check if the departure is after the arrival
                if ($departure > $arrival) {
                    $interval = $arrival->diff($departure);

                    $hours = $interval->days * 24; // Add full days as hours if there are any
                    $hours += $interval->h; // Add hours
                    $hours += ($interval->i / 60); // Add minutes as a fraction of an hour
                    $hours += ($interval->s / 3600); // Add seconds as a fraction of an hour
            
                    $timeTrack->update([
                        'departure_time' => $formattedTime,
                        'hours' => round($hours, 2) // Round to 2 decimal places for better readability
                    ]);
                } else {
                    // If departure is not after arrival, set hours to 0 and update the departure time
                    $timeTrack->update([
                        'departure_time' => $formattedTime,
                        'hours' => 0
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function getStatus(Request $request)
    {
        $userId = Auth::id();
        $date = $request->input('date');

        // Retrieve the time tracking record for the given date
        $timeTrack = TeamManagement::where('user_id', $userId)
                                ->where('date', $date)
                                ->first();

        return response()->json([
            'arrival_time' => $timeTrack ? $timeTrack->arrival_time : null,
            'departure_time' => $timeTrack ? $timeTrack->departure_time : null
        ]);
    }
}