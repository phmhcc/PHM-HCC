@extends('layouts.dashboard')

@section('title', __($definition['title']).' | PHM HCC')
@section('page-heading', __($definition['title']))

@section('content')
<header class="resource-page-heading">
    <div>
        <span class="role-eyebrow">{{ __('Church management') }}</span>
        <h1>{{ __($definition['title']) }}</h1>
        <p>
            @if(auth()->user()->hasPermissionTo($resource.'.create'))
                {{ __('Create, update, and manage :resource.', ['resource' => strtolower(__($definition['title']))]) }}
            @else
                {{ __('View available :resource.', ['resource' => strtolower(__($definition['title']))]) }}
            @endif
        </p>
    </div>
</header>

@if($resource === 'users' && auth()->user()->role === 'admin')
<div style="margin-bottom: 20px;">
    <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.roles.index') }}">
        <i class="fas fa-user-shield" aria-hidden="true"></i> {{ __('Manage roles and permissions') }}
    </a>
</div>
@endif

@if(auth()->user()->hasPermissionTo($resource.'.create'))
<section class="content-box" style="margin-bottom: 24px;">
    <h2 class="resource-panel-title"><i class="fas fa-circle-plus"></i> {{ __('Add a record') }}</h2>
    <form id="createResourceForm" method="POST" action="{{ route('dashboard.resource.store', $resource) }}" enctype="multipart/form-data">
        @csrf
        @if($resource === 'users')
            <p class="resource-form-hint">{{ __('The user will receive an email invitation to set their own password.') }}</p>
        @endif
        <div class="resource-form-grid">
            @foreach($definition['fields'] as $name => $field)
                @if(! ($resource === 'users' && $name === 'password'))
                <label class="resource-field">
                    <span>{{ __($field[0]) }}</span>
                    @if($field[1] === 'textarea')
                        <textarea name="{{ $name }}" rows="3" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>{{ old($name) }}</textarea>
                    @elseif($field[1] === 'select')
                        <select name="{{ $name }}" required>
                            @foreach($field[3] as $option)
                                <option value="{{ $option }}" @selected(old($name, $option === ($field[3][0] ?? null) ? $option : '') === $option)>{{ __(str($option)->replace('_', ' ')->title()->toString()) }}</option>
                            @endforeach
                        </select>
                    @elseif($field[1] === 'file')
                        <input type="file" name="{{ $name }}" accept="{{ $resource === 'finance' ? '.pdf,.jpg,.jpeg,.png' : '.pdf,.xls,.xlsx' }}" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                        @if($resource === 'finance')
                            <small>{{ __('Optional receipt: PDF or image, up to 10 MB.') }}</small>
                        @else
                            <small>{{ __('Attach one PDF or Excel file (up to 10 MB).') }}</small>
                        @endif
                    @elseif($field[1] === 'department' || $field[1] === 'user')
                        @php
                            $options = $field[1] === 'department' ? $departments : $users;
                        @endphp
                        <select name="{{ $name }}">
                            <option value="">{{ __('Not assigned') }}</option>
                            @foreach($options as $option)
                                <option value="{{ $option->id }}" @selected(old($name) == $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $field[1] }}" name="{{ $name }}" value="{{ old($name) }}" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                    @endif
                    @if($name === 'published_at')
                        <small>{{ __('Leave blank to publish now.') }}</small>
                    @endif
                    @error($name)<small class="form-error">{{ $message }}</small>@enderror
                </label>
                @endif
            @endforeach
        </div>
        <button class="dashboard-action" type="submit">{{ __('Create') }}</button>
    </form>
</section>
@endif

<section class="content-box">
    <div class="resource-records-heading">
        <h2>{{ __('Current records') }}</h2>
        <span>{{ $records->total() }} {{ strtolower(__($definition['title'])) }}</span>
    </div>
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    @foreach($definition['columns'] as $column)<th>{{ __(str($column)->replace('_', ' ')->lower()->ucfirst()->toString()) }}</th>@endforeach
                    @if(in_array($resource, ['reports', 'finance'], true))<th>{{ __($resource === 'finance' ? 'Receipt' : 'Attachment') }}</th>@endif
                    @if($resource === 'reports' || auth()->user()->hasPermissionTo($resource.'.update') || auth()->user()->hasPermissionTo($resource.'.delete'))
                        <th>{{ __('Actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    @php
                        $canManageRecord = ! isset($definition['owner'])
                            || auth()->user()->hasPermissionTo($resource.'.view_all')
                            || (int) $record->{$definition['owner']} === (int) auth()->id();
                    @endphp
                    <tr>
                        @foreach($definition['columns'] as $column)
                            @php
                                $value = $record->{$column} ?? null;
                                if (in_array($column, ['assigned_to', 'leader_id'], true) && $value) {
                                    $value = $users->firstWhere('id', $value)?->name;
                                } elseif (in_array($column, ['start_date', 'end_date'], true) && $value) {
                                    $value = \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
                                } elseif (in_array($column, ['duty_date', 'meeting_date', 'reporting_period', 'transaction_date', 'published_at', 'created_at'], true) && $value) {
                                    $value = \Carbon\Carbon::parse($value)->format('d M Y');
                                } elseif ($column === 'amount' && $value !== null) {
                                    $value = number_format((float) $value, 2);
                                }
                            @endphp
                            <td>
                                @if($column === 'status' && $value)
                                    @php
                                        $statusClass = match (strtolower((string) $value)) {
                                            'completed', 'approved', 'reviewed' => 'status-approved',
                                            'cancelled', 'rejected' => 'status-rejected',
                                            default => 'status-pending',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ __(ucfirst($value)) }}</span>
                                @elseif($column === 'type' && $value)
                                    <span class="status-badge {{ $value === 'income' ? 'status-approved' : 'status-rejected' }}">{{ __(ucfirst($value)) }}</span>
                                @else
                                    {{ $value ?: '—' }}
                                @endif
                            </td>
                        @endforeach
                        @if($resource === 'reports')
                            <td>
                                @if(\Illuminate\Support\Facades\Storage::disk('local')->exists('reports/'.$record->id.'/attachment.pdf')
                                    || \Illuminate\Support\Facades\Storage::disk('local')->exists('reports/'.$record->id.'/attachment.xls')
                                    || \Illuminate\Support\Facades\Storage::disk('local')->exists('reports/'.$record->id.'/attachment.xlsx'))
                                    <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.reports.attachment', $record->id) }}">{{ __('Download') }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        @elseif($resource === 'finance')
                            <td>
                                @if($record->receipt_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($record->receipt_path))
                                    <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.finance.receipt', $record->id) }}">{{ __('Download') }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        @endif
                        @if($resource === 'reports' || auth()->user()->hasPermissionTo($resource.'.update') || auth()->user()->hasPermissionTo($resource.'.delete'))
                        <td>
                            <div class="resource-row-actions">
                            @if($resource === 'reports')
                                <a class="dashboard-action dashboard-action-secondary" href="{{ route('dashboard.reports.print', $record->id) }}" target="_blank" rel="noopener">{{ __('Print') }}</a>
                            @endif
                            @if($canManageRecord && auth()->user()->hasPermissionTo($resource.'.update'))
                            <details>
                                <summary class="dashboard-action dashboard-action-secondary">{{ __('Edit') }}</summary>
                                <form method="POST" action="{{ route('dashboard.resource.update', [$resource, $record->id]) }}" class="resource-edit-form" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    @foreach($definition['fields'] as $name => $field)
                                        @php
                                            $value = old($name, $record->{$name} ?? '');
                                            if ($field[1] === 'datetime-local' && $value) {
                                                $value = \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i');
                                            } elseif (in_array($field[1], ['date', 'time'], true) && $value) {
                                                $value = \Carbon\Carbon::parse($value)->format($field[1] === 'date' ? 'Y-m-d' : 'H:i');
                                            }
                                        @endphp
                                        <label class="resource-field">
                                            <span>{{ __($field[0]) }}</span>
                                            @if($field[1] === 'textarea')
                                                <textarea name="{{ $name }}" rows="3">{{ $value }}</textarea>
                                            @elseif($field[1] === 'select')
                                                @php
                                                    $selectOptions = $resource === 'users' && $record->role === 'admin'
                                                        ? ['admin']
                                                        : $field[3];
                                                @endphp
                                                <select name="{{ $name }}" required>
                                                    @foreach($selectOptions as $option)<option value="{{ $option }}" @selected($value === $option)>{{ __(str($option)->replace('_', ' ')->title()->toString()) }}</option>@endforeach
                                                </select>
                                            @elseif($field[1] === 'file')
                                                <input type="file" name="{{ $name }}" accept="{{ $resource === 'finance' ? '.pdf,.jpg,.jpeg,.png' : '.pdf,.xls,.xlsx' }}">
                                                @if($resource === 'finance')
                                                    @if($record->receipt_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($record->receipt_path))
                                                        <a href="{{ route('dashboard.finance.receipt', $record->id) }}">{{ __('Download current receipt') }}</a>
                                                    @endif
                                                    <small>{{ __('Optional replacement, PDF or image up to 10 MB.') }}</small>
                                                @else
                                                    @if(\Illuminate\Support\Facades\Storage::disk('local')->exists('reports/'.$record->id.'/attachment.pdf')
                                                        || \Illuminate\Support\Facades\Storage::disk('local')->exists('reports/'.$record->id.'/attachment.xls')
                                                        || \Illuminate\Support\Facades\Storage::disk('local')->exists('reports/'.$record->id.'/attachment.xlsx'))
                                                        <a href="{{ route('dashboard.reports.attachment', $record->id) }}">{{ __('Download current attachment') }}</a>
                                                    @endif
                                                    <small>{{ __('Optional replacement, PDF or Excel up to 10 MB.') }}</small>
                                                @endif
                                            @elseif($field[1] === 'department' || $field[1] === 'user')
                                                @php
                                                    $options = $field[1] === 'department' ? $departments : $users;
                                                @endphp
                                                <select name="{{ $name }}">
                                                    <option value="">{{ __('Not assigned') }}</option>
                                                    @foreach($options as $option)<option value="{{ $option->id }}" @selected((string) $value === (string) $option->id)>{{ $option->name }}</option>@endforeach
                                                </select>
                                            @else
                                                <input type="{{ $field[1] }}" name="{{ $name }}" value="{{ $field[1] === 'password' ? '' : $value }}" {{ str_starts_with($field[2], 'required') && $field[1] !== 'password' ? 'required' : '' }}>
                                            @endif
                                            @if($name === 'published_at')
                                                <small>{{ __('Leave blank to publish now.') }}</small>
                                            @endif
                                            @error($name)<small class="form-error">{{ $message }}</small>@enderror
                                        </label>
                                    @endforeach
                                    <button class="dashboard-action" type="submit">{{ __('Save changes') }}</button>
                                </form>
                            </details>
                            @endif
                            @if($canManageRecord && auth()->user()->hasPermissionTo($resource.'.delete'))
                            <form method="POST" action="{{ route('dashboard.resource.destroy', [$resource, $record->id]) }}" class="resource-delete-form" data-confirm-delete>
                                @csrf
                                @method('DELETE')
                                <button class="dashboard-action dashboard-action-danger" type="submit">{{ __('Delete') }}</button>
                            </form>
                            @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ count($definition['columns']) + (in_array($resource, ['reports', 'finance'], true) ? 1 : 0) + (($resource === 'reports' || auth()->user()->hasPermissionTo($resource.'.update') || auth()->user()->hasPermissionTo($resource.'.delete')) ? 1 : 0) }}">{{ __('No records have been added.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $records->links() }}
</section>
@endsection
