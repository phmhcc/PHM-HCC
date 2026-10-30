<?php

namespace App\Http\Controllers;


use App\Models\Duty;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\Report;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $events = Event::where('start_date', '>=', now())->orderBy('start_date')->take(5)->get();
        $duties = Duty::with('assignedUser', 'department')->whereDate('duty_date', '>=', today())->orderBy('duty_date')->take(5)->get();
        $notifications = DatabaseNotification::query()
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', get_class(Auth::user()))
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();
        $upcomingEvents = Event::where('start_date', '>=', now())->count();
        $scheduledDuties = Duty::whereDate('duty_date', '>=', today())->count();
        $pendingReports = Report::where('status', 'pending')->count();
        $upcomingMeetings = Meeting::whereDate('meeting_date', '>=', today())->count();
        $financeTotals = collect();
        if (in_array(Auth::user()->role, ['admin', 'treasurer'], true)) {
            $financeTotals = FinancialTransaction::query()
                ->selectRaw('type, SUM(amount) as total')
                ->groupBy('type')
                ->pluck('total', 'type');
        }

        return view('dashboards.index', compact(
            'events',
            'duties',
            'notifications',
            'upcomingEvents',
            'scheduledDuties',
            'pendingReports',
            'upcomingMeetings',
            'financeTotals'
        ));
    }
}
