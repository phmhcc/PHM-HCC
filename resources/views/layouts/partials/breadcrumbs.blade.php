@php
    $currentRoute = request()->route()?->getName();
    $breadcrumbs = [];
    // $homeCrumb = ['label' => __('Home'), 'url' => route('home')];
    $dashboardCrumb = ['label' => __('Dashboard'), 'url' => route('dashboard')];

    // $publicPages = [
    //     'about' => 'About',
    //     'leadership' => 'Leadership',
    //     'ministries' => 'Ministries',
    //     'gallery' => 'Gallery',
    //     'announcements' => 'Announcements',
    //     'contact' => 'Contact',
    // ];

    $dashboardPages = [
        'dashboard.deacon' => 'Deacon Dashboard',
        'dashboard.elder' => 'Elder Dashboard',
        'dashboard.department-leader' => 'Department Leader Dashboard',
        'dashboard.treasurer' => 'Treasurer Dashboard',
        'dashboard.notifications' => 'Notifications',
        'dashboard.profile' => 'My Profile',
    ];

    $resourcePages = [
        'events' => 'Weekly Activities',
        'announcements' => 'Announcements',
        'duties' => 'Duty Schedule',
        'meetings' => 'Meetings',
        'departments' => 'Departments',
        'reports' => 'Reports',
        'finance' => 'Financial Statements',
        'users' => 'User Management',
    ];

    if (isset($publicPages[$currentRoute])) {
        $breadcrumbs = [$homeCrumb, ['label' => __($publicPages[$currentRoute])]];
    } elseif ($currentRoute === 'login') {
        $breadcrumbs = [$homeCrumb, ['label' => __('Sign in')]];
    } elseif ($currentRoute === 'password.request') {
        $breadcrumbs = [
            $homeCrumb,
            ['label' => __('Sign in'), 'url' => route('login')],
            ['label' => __('Forgot Your Password?')],
        ];
    } elseif ($currentRoute === 'password.reset') {
        $breadcrumbs = [
            $homeCrumb,
            ['label' => __('Sign in'), 'url' => route('login')],
            ['label' => __('Choose a new password')],
        ];
    } elseif ($currentRoute === 'dashboard') {
        $breadcrumbs = [['label' => __('Dashboard')]];
    } elseif (isset($dashboardPages[$currentRoute])) {
        $breadcrumbs = [
            $dashboardCrumb,
            ['label' => __($dashboardPages[$currentRoute])],
        ];
    } elseif ($currentRoute === 'dashboard.resource.index') {
        $resource = request()->route('resource');
        $resourceLabel = $resourcePages[$resource] ?? str($resource)->headline()->toString();
        $breadcrumbs = [
            $dashboardCrumb,
            ['label' => __($resourceLabel)],
        ];
    } elseif ($currentRoute === 'dashboard.reports.print') {
        $breadcrumbs = [
            $dashboardCrumb,
            ['label' => __('Reports'), 'url' => route('dashboard.resource.index', 'reports')],
            ['label' => __('Print report')],
        ];
    } elseif ($currentRoute === 'dashboard.roles.index') {
        $breadcrumbs = [
            $dashboardCrumb,
            ['label' => __('Roles and permissions')],
        ];
    } elseif (in_array($currentRoute, ['dashboard.roles.create', 'dashboard.roles.show', 'dashboard.roles.edit'], true)) {
        $breadcrumbs = [
            $dashboardCrumb,
            ['label' => __('Roles and permissions'), 'url' => route('dashboard.roles.index')],
        ];

        if ($currentRoute !== 'dashboard.roles.create' && isset($role)) {
            $roleLabel = __(str($role->name)->replace('_', ' ')->title()->toString());
            $breadcrumbs[] = [
                'label' => $roleLabel,
                'url' => $currentRoute === 'dashboard.roles.edit' ? route('dashboard.roles.show', $role) : null,
            ];
        }

        if ($currentRoute === 'dashboard.roles.create') {
            $breadcrumbs[] = ['label' => __('Create role')];
        } elseif ($currentRoute === 'dashboard.roles.edit') {
            $breadcrumbs[] = ['label' => __('Edit role')];
        }
    }
@endphp

@if($breadcrumbs)
    <nav class="breadcrumbs" aria-label="{{ __('Breadcrumb') }}">
        <ol>
            @foreach($breadcrumbs as $breadcrumb)
                <li>
                    @if(! empty($breadcrumb['url']) && ! $loop->last)
                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                    @else
                        <span aria-current="page">{{ $breadcrumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
