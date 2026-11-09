<?php

namespace App\Http\Controllers;


use App\Models\Duty;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\Report;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        Event::markPastEventsAsCompleted();
        $eventsQuery = Event::where('start_date', '>=', now());
        if (! $user->hasPermissionTo('events.view_all')) {
            $eventsQuery->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereIn('created_by', User::query()->where('role', 'admin')->select('id'));
            });
        }
        $events = (clone $eventsQuery)->orderBy('start_date')->take(5)->get();

        $dutiesQuery = Duty::whereDate('duty_date', '>=', today());
        if (! $user->hasPermissionTo('duties.view_all')) {
            $dutiesQuery->where(function ($query) use ($user) {
                $query->where('created_by', $user->id)
                    ->orWhereIn('created_by', User::query()->where('role', 'admin')->select('id'));
            });
        }
        $duties = (clone $dutiesQuery)->with('assignedUser', 'department')->orderBy('duty_date')->take(5)->get();
        $notifications = DatabaseNotification::query()
            ->where('notifiable_id', Auth::id())
            ->where('notifiable_type', get_class(Auth::user()))
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();
        $upcomingEvents = $eventsQuery->count();
        $scheduledDuties = $dutiesQuery->count();
        $reportsQuery = Report::where('status', 'pending');
        if (! $user->hasPermissionTo('reports.view_all')) {
            $reportsQuery->where('submitted_by', $user->id);
        }
        $pendingReports = $reportsQuery->count();
        $upcomingMeetings = Meeting::whereDate('meeting_date', '>=', today())->count();
        $financeTotals = collect();
        if ($user->hasPermissionTo('finance.view')) {
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
