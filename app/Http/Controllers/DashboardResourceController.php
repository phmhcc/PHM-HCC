<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Department;
use App\Models\Duty;
use App\Models\Event;
use App\Models\FinancialTransaction;
use App\Models\Meeting;
use App\Models\Role;
use App\Models\Report;
use App\Models\User;
use App\Notifications\AnnouncementPublished;
use App\Notifications\AccountInvitation;
use App\Notifications\DashboardInformationPublished;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

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
                'title' => ['Duty', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'duty_date' => ['Date', 'date', 'required|date'], 'start_time' => ['Start time', 'time', 'nullable|date_format:H:i'], 'end_time' => ['End time', 'time', 'nullable|date_format:H:i'], 'assigned_to' => ['Assigned to', 'user', 'nullable|exists:users,id'], 'status' => ['Status', 'select', 'required|in:pending,completed,cancelled', ['pending', 'completed', 'cancelled']],
            ]],
            'meetings' => ['title' => 'Meetings', 'model' => Meeting::class, 'owner' => 'created_by', 'columns' => ['title', 'meeting_date', 'venue', 'status'], 'fields' => [
                'title' => ['Title', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'meeting_date' => ['Date', 'date', 'required|date'], 'start_time' => ['Start time', 'time', 'nullable|date_format:H:i'], 'end_time' => ['End time', 'time', 'nullable|date_format:H:i'], 'venue' => ['Venue', 'text', 'nullable|string|max:255'], 'department_id' => ['Department', 'department', 'nullable|exists:departments,id'], 'status' => ['Status', 'select', 'required|in:scheduled,completed,cancelled', ['scheduled', 'completed', 'cancelled']],
            ]],
            'departments' => ['title' => 'Departments', 'model' => Department::class, 'columns' => ['name', 'leader_id'], 'fields' => [
                'name' => ['Name', 'text', 'required|string|max:255'], 'description' => ['Description', 'textarea', 'nullable|string'], 'leader_id' => ['Leader', 'user', 'nullable|exists:users,id'],
            ]],
            'reports' => ['title' => 'Reports', 'model' => Report::class, 'owner' => 'submitted_by', 'columns' => ['title', 'reporting_period', 'status'], 'fields' => [
                'title' => ['Title', 'text', 'required|string|max:255'], 'department_id' => ['Department', 'department', 'nullable|exists:departments,id'], 'reporting_period' => ['Reporting period', 'date', 'nullable|date'], 'status' => ['Status', 'select', 'required|in:pending,reviewed,approved', ['pending', 'reviewed', 'approved']],
                'attachment' => ['Attachment', 'file', 'nullable|file|mimes:pdf,xls,xlsx|extensions:pdf,xls,xlsx|max:10240'],
            ]],
            'finance' => ['title' => 'Financial Transactions', 'model' => FinancialTransaction::class, 'columns' => ['transaction_date', 'type', 'category', 'amount', 'description'], 'fields' => [
                'transaction_date' => ['Date', 'date', 'required|date'], 'type' => ['Type', 'select', 'required|in:income,expense', ['income', 'expense']], 'category' => ['Category', 'select', 'required|in:Tithe,Offering,Thanksgiving,Vows,Ujenzi,Supporting', ['Tithe', 'Offering', 'Thanksgiving', 'Vows', 'Ujenzi', 'Supporting']], 'amount' => ['Amount', 'number', 'required|numeric|min:0.01'], 'description' => ['Description', 'textarea', 'nullable|string'], 'reference' => ['Reference', 'text', 'nullable|string|max:255'], 'receipt' => ['Receipt attachment', 'file', 'nullable|file|mimes:pdf,jpg,jpeg,png|extensions:pdf,jpg,jpeg,png|max:10240'],
            ]],
            'users' => ['title' => 'User Management', 'model' => User::class, 'columns' => ['name', 'email', 'department_id', 'role', 'created_at'], 'fields' => [
                'name' => ['Name', 'text', 'required|string|max:255'], 'email' => ['Email', 'email', 'required|email|max:255'], 'department_id' => ['Department', 'department', 'nullable|exists:departments,id'], 'role' => ['Role', 'select', 'required|exists:roles,name|not_in:admin', Role::where('name', '!=', 'admin')->orderBy('name')->pluck('name')->all()], 'password' => ['Password', 'password', 'required|string|min:1'],
            ]],
        ];
    }

    private function definition(string $resource, string $action = 'view'): array
    {
        abort_unless(isset($this->definitions()[$resource]), 404);
        $definition = $this->definitions()[$resource];
        abort_unless(request()->user()->hasPermissionTo($resource.'.'.$action), 403);

        return $definition;
    }

    public function index(string $resource): View
    {
        $definition = $this->definition($resource);
        $query = $definition['model']::query()->latest('id');
        if (isset($definition['owner']) && ! request()->user()->hasPermissionTo($resource.'.view_all')) {
            if (in_array($resource, ['events', 'duties', 'meetings'], true)) {
                $query->where(function ($query) use ($definition) {
                    $query->where($definition['owner'], request()->user()->getAuthIdentifier())
                        ->orWhereIn(
                            $definition['owner'],
                            User::query()->where('role', 'admin')->select('id')
                        );
                });
            } else {
                $query->where($definition['owner'], request()->user()->getAuthIdentifier());
            }
        }

        $records = $query->paginate(15);
        $departments = Department::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('dashboards.resources.index', compact('resource', 'definition', 'records', 'departments', 'users'));
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->definition($resource, 'create');
        $data = $this->validated($request, $definition);
        if ($resource === 'announcements' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }
        $attachment = $data['attachment'] ?? $data['receipt'] ?? null;
        unset($data['attachment'], $data['receipt']);
        $record = new $definition['model'];
        $record->fill($data);
        if (isset($definition['owner'])) {
            $record->{$definition['owner']} = request()->user()->getAuthIdentifier();
        }
        if ($resource === 'users') {
            $record->password = Str::random(64);
        }
        if ($resource === 'finance') {
            $record->recorded_by = request()->user()->getAuthIdentifier();
        }

        if ($resource === 'users') {
            DB::transaction(function () use ($record): void {
                $record->save();
                $token = Password::createToken($record);
                $record->notify(new AccountInvitation($token));
            });

            return back()->with('status', __('User created and password setup invitation sent.'));
        }

        $record->save();
        if ($resource === 'reports' && $attachment instanceof UploadedFile) {
            $this->storeReportAttachment($record, $attachment);
        } elseif ($resource === 'finance' && $attachment instanceof UploadedFile) {
            $this->storeFinanceReceipt($record, $attachment);
        }

        if ($resource === 'announcements') {
            $notification = new AnnouncementPublished($record);
        } elseif (
            request()->user()->role === 'admin'
            && in_array($resource, ['events', 'duties', 'meetings', 'departments'], true)
        ) {
            $noun = [
                'events' => 'event',
                'duties' => 'duty',
                'meetings' => 'meeting',
                'departments' => 'department',
            ][$resource];
            $itemTitle = $resource === 'departments' ? $record->name : $record->title;
            $titleTranslation = 'New '.$noun.': :title';
            $titleParameters = ['title' => $itemTitle];
            $messageTranslation = 'An administrator added this to the church dashboard.';
            $notification = new DashboardInformationPublished(
                __($titleTranslation, $titleParameters),
                __($messageTranslation),
                $titleTranslation,
                $titleParameters,
                $messageTranslation,
            );
        }

        if (isset($notification)) {
            User::where('id', '!=', request()->user()->getAuthIdentifier())
                ->each(fn (User $user) => $user->notify($notification));
        }

        return back()->with('status', __(':resource item created.', ['resource' => __($definition['title'])]));
    }

    public function update(Request $request, string $resource, int $record): RedirectResponse
    {
        $definition = $this->definition($resource, 'update');
        $item = $this->findRecord($definition, $record, $resource);
        $data = $this->validated($request, $definition, $record);
        if ($resource === 'announcements' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }
        $attachment = $data['attachment'] ?? $data['receipt'] ?? null;
        unset($data['attachment'], $data['receipt']);
        if ($resource === 'users') {
            if (empty($data['password'])) {
                unset($data['password']);
            }
        }
        $item->fill($data)->save();
        if ($resource === 'reports' && $attachment instanceof UploadedFile) {
            $this->storeReportAttachment($item, $attachment);
        } elseif ($resource === 'finance' && $attachment instanceof UploadedFile) {
            $this->storeFinanceReceipt($item, $attachment);
        }

        return back()->with('status', __(':resource item updated.', ['resource' => __($definition['title'])]));
    }

    public function downloadReportAttachment(int $record)
    {
        $definition = $this->definition('reports');
        $report = $this->findRecord($definition, $record, 'reports');
        $path = $this->reportAttachmentPath($report);

        abort_unless($path !== null, 404);

        return response()->download(Storage::disk('local')->path($path));
    }

    public function downloadFinanceReceipt(int $record)
    {
        $this->definition('finance');
        $transaction = FinancialTransaction::findOrFail($record);
        $path = $transaction->receipt_path;

        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        return response()->download(Storage::disk('local')->path($path));
    }

    public function printReport(int $record): View
    {
        $definition = $this->definition('reports');
        $report = $this->findRecord($definition, $record, 'reports');
        $report->load(['department', 'submitter']);

        return view('dashboards.resources.report-print', compact('report'));
    }

    public function destroy(string $resource, int $record): RedirectResponse
    {
        $definition = $this->definition($resource, 'delete');
        $item = $this->findRecord($definition, $record, $resource);
        abort_if($resource === 'users' && $item->is(request()->user()), 422, __('You cannot delete your own account.'));
        $item->delete();
        if ($resource === 'reports') {
            $this->deleteReportAttachment($item);
        } elseif ($resource === 'finance' && $item->receipt_path) {
            Storage::disk('local')->delete($item->receipt_path);
        }

        return back()->with('status', __(':resource item deleted.', ['resource' => __($definition['title'])]));
    }

    private function findRecord(array $definition, int $id, string $resource): Model
    {
        $query = $definition['model']::query();
        $user = request()->user();
        if (
            isset($definition['owner'])
            && $user !== null
            && ! $user->hasPermissionTo($resource.'.view_all')
        ) {
            $query->where($definition['owner'], $user->getAuthIdentifier());
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
        } elseif (isset($rules['password']) && $definition['model'] === User::class) {
            $rules['password'] = 'nullable|string';
        }
        if (
            isset($rules['role'])
            && $record !== null
            && $definition['model'] === User::class
            && User::whereKey($record)->value('role') === 'admin'
        ) {
            $rules['role'] = 'required|in:admin';
        }
        if (isset($rules['email'])) {
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($record)];
        }

        return $request->validate($rules, [
            'email.unique' => __('This email address is already registered.'),
        ]);
    }

    private function storeReportAttachment(Report $report, UploadedFile $attachment): void
    {
        $extension = strtolower($attachment->getClientOriginalExtension());
        $directory = 'reports/'.$report->getKey();
        $path = $attachment->storeAs($directory, 'attachment.'.$extension, 'local');

        if (! is_string($path)) {
            throw new RuntimeException('The report attachment could not be saved.');
        }

        foreach (['pdf', 'xls', 'xlsx'] as $oldExtension) {
            $oldPath = $directory.'/attachment.'.$oldExtension;
            if ($oldPath !== $path && Storage::disk('local')->exists($oldPath)) {
                Storage::disk('local')->delete($oldPath);
            }
        }
    }

    private function storeFinanceReceipt(FinancialTransaction $transaction, UploadedFile $receipt): void
    {
        $extension = strtolower($receipt->getClientOriginalExtension());
        $path = $receipt->storeAs(
            'finance/'.$transaction->getKey(),
            'receipt.'.$extension,
            'local'
        );

        if (! is_string($path)) {
            throw new RuntimeException('The finance receipt could not be saved.');
        }

        $oldPath = $transaction->receipt_path;
        $transaction->receipt_path = $path;
        $transaction->save();

        if ($oldPath && $oldPath !== $path && Storage::disk('local')->exists($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }
    }

    private function reportAttachmentPath(Report $report): ?string
    {
        foreach (['pdf', 'xls', 'xlsx'] as $extension) {
            $path = 'reports/'.$report->getKey().'/attachment.'.$extension;
            if (Storage::disk('local')->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    private function deleteReportAttachment(Report $report): void
    {
        $path = $this->reportAttachmentPath($report);
        if ($path !== null) {
            Storage::disk('local')->delete($path);
        }
    }
}
