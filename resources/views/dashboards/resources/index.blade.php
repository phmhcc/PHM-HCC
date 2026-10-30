@extends('layouts.dashboard')

@section('title', $definition['title'].' | PHM HCC')
@section('page-heading', $definition['title'])

@section('content')
<header class="resource-page-heading">
    <div>
        <span class="role-eyebrow">Church management</span>
        <h1>{{ $definition['title'] }}</h1>
        <p>Create, update, and manage {{ strtolower($definition['title']) }}.</p>
    </div>
</header>

<section class="content-box" style="margin-bottom: 24px;">
    <h2 class="resource-panel-title"><i class="fas fa-circle-plus"></i> Add a record</h2>
    <form method="POST" action="{{ route('dashboard.resource.store', $resource) }}">
        @csrf
        <div class="resource-form-grid">
            @foreach($definition['fields'] as $name => $field)
                <label class="resource-field">
                    <span>{{ $field[0] }}</span>
                    @if($field[1] === 'textarea')
                        <textarea name="{{ $name }}" rows="3" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>{{ old($name) }}</textarea>
                    @elseif($field[1] === 'select')
                        <select name="{{ $name }}" required>
                            @foreach($field[3] as $option)
                                <option value="{{ $option }}" @selected(old($name, $option === ($field[3][0] ?? null) ? $option : '') === $option)>{{ ucfirst($option) }}</option>
                            @endforeach
                        </select>
                    @elseif($field[1] === 'department' || $field[1] === 'user')
                        @php
                            $options = $field[1] === 'department' ? $departments : $users;
                        @endphp
                        <select name="{{ $name }}">
                            <option value="">Not assigned</option>
                            @foreach($options as $option)
                                <option value="{{ $option->id }}" @selected(old($name) == $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $field[1] }}" name="{{ $name }}" value="{{ old($name) }}" {{ str_starts_with($field[2], 'required') ? 'required' : '' }}>
                    @endif
                    @error($name)<small class="form-error">{{ $message }}</small>@enderror
                </label>
            @endforeach
        </div>
        <button class="dashboard-action" type="submit">Create</button>
    </form>
</section>

<section class="content-box">
    <div class="resource-records-heading">
        <h2>Current records</h2>
        <span>{{ $records->total() }} {{ str($definition['title'])->lower() }}</span>
    </div>
    <div class="table-responsive">
        <table class="dashboard-table">
            <thead>
                <tr>
                    @foreach($definition['columns'] as $column)<th>{{ str($column)->replace('_', ' ')->title() }}</th>@endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
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
                                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($value) }}</span>
                                @elseif($column === 'type' && $value)
                                    <span class="status-badge {{ $value === 'income' ? 'status-approved' : 'status-rejected' }}">{{ ucfirst($value) }}</span>
                                @else
                                    {{ $value ?: '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <details>
                                <summary class="dashboard-action dashboard-action-secondary">Edit</summary>
                                <form method="POST" action="{{ route('dashboard.resource.update', [$resource, $record->id]) }}" class="resource-edit-form">
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
                                            <span>{{ $field[0] }}</span>
                                            @if($field[1] === 'textarea')
                                                <textarea name="{{ $name }}" rows="3">{{ $value }}</textarea>
                                            @elseif($field[1] === 'select')
                                                <select name="{{ $name }}" required>
                                                    @foreach($field[3] as $option)<option value="{{ $option }}" @selected($value === $option)>{{ ucfirst($option) }}</option>@endforeach
                                                </select>
                                            @elseif($field[1] === 'department' || $field[1] === 'user')
                                                @php
                                                    $options = $field[1] === 'department' ? $departments : $users;
                                                @endphp
                                                <select name="{{ $name }}">
                                                    <option value="">Not assigned</option>
                                                    @foreach($options as $option)<option value="{{ $option->id }}" @selected((string) $value === (string) $option->id)>{{ $option->name }}</option>@endforeach
                                                </select>
                                            @else
                                                <input type="{{ $field[1] }}" name="{{ $name }}" value="{{ $field[1] === 'password' ? '' : $value }}" {{ str_starts_with($field[2], 'required') && $field[1] !== 'password' ? 'required' : '' }}>
                                            @endif
                                            @error($name)<small class="form-error">{{ $message }}</small>@enderror
                                        </label>
                                    @endforeach
                                    <button class="dashboard-action" type="submit">Save changes</button>
                                </form>
                            </details>
                            <form method="POST" action="{{ route('dashboard.resource.destroy', [$resource, $record->id]) }}" onsubmit="return confirm('Delete this record?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="dashboard-action dashboard-action-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($definition['columns']) + 1 }}">No records have been added.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $records->links() }}
</section>
@endsection
