<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Department;
use App\Models\Duty;
use App\Models\Event;
use App\Models\FinancialTransaction;
use App\Models\Meeting;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AnnouncementPublished;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DashboardResourceController extends Controller
{
    private function definitions(): array
    {
        return [
            'events' => ['title' => 'Weekly Activities', 'model' => Event::class, 'owner' => 'created_by', 'columns' => ['title', 'start_date', 'venue', 'status'], 'fields' => [
                'title' => ['Title', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'start_date' => ['Starts', 'datetime-local', 'required|date'], 'end_date' => ['Ends', 'datetime-local', 'nullable|date|after_or_equal:start_date'], 'venue' => ['Venue', 'text', 'nullable|string|max:255'], 'status' => ['Status', 'select', 'required|in:scheduled,cancelled,completed', ['scheduled', 'cancelled', 'completed']],
            ]],
            'announcements' => ['title' => 'Announcements', 'model' => Announcement::class, 'owner' => 'created_by', 'columns' => ['title', 'published_at'], 'fields' => [
                'title' => ['Title', 'text', 'required|string|max:255'], 'message' => ['Message', 'textarea', 'required|string'], 'published_at' => ['Publish at', 'datetime-local', 'nullable|date'],
            ]],
            'duties' => ['title' => 'Duty Schedule', 'model' => Duty::class, 'owner' => 'created_by', 'columns' => ['title', 'duty_date', 'assigned_to', 'status'], 'fields' => [
                'title' => ['Duty', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'duty_date' => ['Date', 'date', 'required|date'], 'start_time' => ['Start time', 'time', 'nullable|date_format:H:i'], 'end_time' => ['End time', 'time', 'nullable|date_format:H:i'], 'department_id' => ['Department', 'department', 'nullable|exists:departments,id'], 'assigned_to' => ['Assigned to', 'user', 'nullable|exists:users,id'], 'status' => ['Status', 'select', 'required|in:pending,completed,cancelled', ['pending', 'completed', 'cancelled']],
            ]],
            'meetings' => ['title' => 'Meetings', 'model' => Meeting::class, 'owner' => 'created_by', 'columns' => ['title', 'meeting_date', 'venue', 'status'], 'fields' => [
                'title' => ['Title', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'meeting_date' => ['Date', 'date', 'required|date'], 'start_time' => ['Start time', 'time', 'nullable|date_format:H:i'], 'end_time' => ['End time', 'time', 'nullable|date_format:H:i'], 'venue' => ['Venue', 'text', 'nullable|string|max:255'], 'department_id' => ['Department', 'department', 'nullable|exists:departments,id'], 'status' => ['Status', 'select', 'required|in:scheduled,completed,cancelled', ['scheduled', 'completed', 'cancelled']],
            ]],
            'departments' => ['title' => 'Departments', 'model' => Department::class, 'columns' => ['name', 'leader_id'], 'fields' => [
                'name' => ['Name', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'leader_id' => ['Leader', 'user', 'nullable|exists:users,id'],
            ]],
            'reports' => ['title' => 'Reports', 'model' => Report::class, 'owner' => 'submitted_by', 'columns' => ['title', 'reporting_period', 'status'], 'fields' => [
                'title' => ['Title', 'text', 'required|string|max:255'], 'department_id' => ['Department', 'department', 'nullable|exists:departments,id'], 'reporting_period' => ['Reporting period', 'date', 'nullable|date'], 'activities' => ['Activities', 'textarea', 'nullable|string'], 'achievements' => ['Achievements', 'textarea', 'nullable|string'], 'challenges' => ['Challenges', 'textarea', 'nullable|string'], 'recommendations' => ['Recommendations', 'textarea', 'nullable|string'], 'status' => ['Status', 'select', 'required|in:pending,reviewed,approved', ['pending', 'reviewed', 'approved']],
            ]],
            'finance' => ['title' => 'Financial Transactions', 'model' => FinancialTransaction::class, 'owner' => 'recorded_by', 'admin' => true, 'columns' => ['transaction_date', 'type', 'category', 'amount', 'description'], 'fields' => [
                'transaction_date' => ['Date', 'date', 'required|date'], 'type' => ['Type', 'select', 'required|in:income,expense', ['income', 'expense']], 'category' => ['Category', 'text', 'required|string|max:255'], 'amount' => ['Amount', 'number', 'required|numeric|min:0.01'], 'description' => ['Description', 'textarea', 'nullable|string'], 'reference' => ['Reference', 'text', 'nullable|string|max:255'],
            ]],
            'users' => ['title' => 'User Management', 'model' => User::class, 'admin' => true, 'columns' => ['name', 'email', 'created_at'], 'fields' => [
                'name' => ['Name', 'text', 'required|string|max:255'], 'email' => ['Email', 'email', 'required|email|max:255'], 'password' => ['Password', 'password', 'required|string|min:1'],
            ]],
        ];
    }

    private function definition(string $resource): array
    {
        abort_unless(isset($this->definitions()[$resource]), 404);
        $definition = $this->definitions()[$resource];
        abort_if(($definition['admin'] ?? false) && auth()->user()->email !== config('admin.email'), 403);

        return $definition;
    }

    public function index(string $resource): View
    {
        $definition = $this->definition($resource);
        $query = $definition['model']::query()->latest('id');
        if (isset($definition['owner'])) {
            $query->where($definition['owner'], auth()->id());
        }

        $records = $query->paginate(15);
        $departments = Department::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('dashboards.resources.index', compact('resource', 'definition', 'records', 'departments', 'users'));
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->definition($resource);
        $record = new $definition['model'];
        $record->fill($this->validated($request, $definition));
        if (isset($definition['owner'])) {
            $record->{$definition['owner']} = auth()->id();
        }
        if ($resource === 'users') {
            $record->password = $request->input('password');
        }
        $record->save();

        if ($resource === 'announcements') {
            User::where('id', '!=', auth()->id())->each(fn (User $user) => $user->notify(new AnnouncementPublished($record)));
        }

        return back()->with('status', $definition['title'].' item created.');
    }

    public function update(Request $request, string $resource, int $record): RedirectResponse
    {
        $definition = $this->definition($resource);
        $item = $this->findRecord($definition, $record);
        $data = $this->validated($request, $definition, $record);
        if ($resource === 'users' && empty($data['password'])) {
            unset($data['password']);
        }
        $item->fill($data)->save();

        return back()->with('status', $definition['title'].' item updated.');
    }

    public function destroy(string $resource, int $record): RedirectResponse
    {
        $definition = $this->definition($resource);
        $item = $this->findRecord($definition, $record);
        abort_if($resource === 'users' && $item->is(auth()->user()), 422, 'You cannot delete your own account.');
        $item->delete();

        return back()->with('status', $definition['title'].' item deleted.');
    }

    private function findRecord(array $definition, int $id): Model
    {
        $query = $definition['model']::query();
        if (isset($definition['owner'])) {
            $query->where($definition['owner'], auth()->id());
        }

        return $query->findOrFail($id);
    }

    private function validated(Request $request, array $definition, ?int $record = null): array
    {
        $rules = [];
        foreach ($definition['fields'] as $name => $field) {
            $rules[$name] = $field[2];
        }
        if (isset($rules['password']) && $record !== null) {
            $rules['password'] = 'nullable|string';
        }
        if (isset($rules['email'])) {
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($record)];
        }

        return $request->validate($rules);
    }
}
