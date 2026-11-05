<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', __('Dashboard | PHM HCC'))</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7fb;
            color: #333;
        }

        a {
            text-decoration: none;
        }

        button {
            font-family: inherit;
        }

        .language-switch select {
            min-height: 36px;
            padding: 6px 10px;
            border: 1px solid #d5deea;
            border-radius: 6px;
            background: #fff;
            color: #123b64;
            font: inherit;
        }

        .dashboard-shell {
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #003366;
            color: #fff;
            padding: 25px 18px;
            z-index: 1000;
            overflow-y: auto;
            transition: none !important;
            will-change: auto;

        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 5px 10px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, .15);
        }

        .brand-logo {
            width: 55px;
            height: 55px;
            flex: 0 0 55px;
            border-radius: 50%;
            object-fit: cover;
        }

        .sidebar-brand h2 {
            font-size: 19px;
            font-weight: 700;
        }

        .sidebar-brand small {
            display: block;
            font-size: 10px;
            color: #cbd8e8;
            letter-spacing: 1px;
        }

        .sidebar-label {
            font-size: 10px;
            color: #a9bfd7;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 27px 12px 12px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 6px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 12px 14px;
            color: #d8e4f1;
            border-radius: 8px;
            font-size: 13px;
            transition: .2s;
        }

        .sidebar-menu a i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #c9a900;
            color: #003366;
            font-weight: 600;
        }

        .sidebar-footer {
            margin-top: 35px;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, .15);
            color: #b9cce0;
            font-size: 11px;
            text-align: center;
        }

        /* MAIN AREA */

        .main-area {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .dashboard-content {
            flex: 1;
            width: 100%;
        }

        .dashboard-footer {
            width: 100%;
            flex-shrink: 0;
            padding: 16px 32px;
            border-top: 1px solid #dce5ef;
            background: #eef2f6;
            color: #607b99;
            font-size: 11px;
            line-height: 20px;
        }

        /* TOP NAVBAR */

        .topbar {
            height: 75px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .05);
            position: sticky;
            top: 0;
            z-index: 1200;
            isolation: isolate;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .menu-toggle {
            border: none;
            background: none;
            font-size: 21px;
            color: #003366;
            cursor: pointer;
            display: none;
            padding: 8px;
            transition: none;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .topbar-icon {
            position: relative;
            color: #526276;
            font-size: 19px;
        }

        .notification-dot {
            position: absolute;
            top: -5px;
            right: -6px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #dc3545;
            border: 1px solid white;
        }

        .profile {
            position: relative;
            z-index: 1;
        }

        .profile-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px;
            border: 0;
            border-radius: 9px;
            background: transparent;
            cursor: pointer;
            text-align: left;
        }

        .profile-toggle:hover,
        .profile-toggle[aria-expanded="true"] {
            background: #f4f7fb;
        }

        .profile-chevron {
            margin-left: 4px;
            color: #718096;
            font-size: 11px;
            transition: transform .2s ease;
        }

        .profile-toggle[aria-expanded="true"] .profile-chevron {
            transform: rotate(180deg);
        }

        .profile-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1300;
            display: none;
            width: 210px;
            padding: 7px;
            border: 1px solid #e8edf4;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(25, 45, 75, .16);
        }

        .profile-menu.open {
            display: block;
        }

        .profile-menu a,
        .profile-menu button {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #34465c;
            font: inherit;
            font-size: 12px;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
        }

        .profile-menu a:hover,
        .profile-menu button:hover {
            background: #f4f7fb;
            color: #003366;
        }

        .profile-menu i {
            width: 17px;
            color: #61758c;
            text-align: center;
        }

        .profile-menu form {
            margin: 0;
            border-top: 1px solid #edf0f5;
            padding-top: 6px;
            margin-top: 5px;
        }

        .profile-menu form button {
            color: #b42318;
        }

        .profile-menu form button i {
            color: #b42318;
        }

        .profile-avatar {
            width: 39px;
            height: 39px;
            background: #003366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-info strong {
            display: block;
            font-size: 12px;
            color: #003366;
        }

        .profile-info small {
            color: #999;
            font-size: 10px;
        }

        /* DASHBOARD CONTENT */

        .dashboard-content {
            padding: 32px 35px;
            max-width: 1600px;
            margin: auto;
        }

        .dashboard-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 28px;
        }

        .dashboard-heading h1 {
            color: #003366;
            font-size: 24px;
            font-weight: 700;
        }

        .dashboard-heading p {
            color: #888;
            font-size: 12px;
            margin-top: 5px;
        }

        .date-display {
            background: #fff;
            padding: 11px 16px;
            border-radius: 8px;
            color: #003366;
            font-size: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, .04);
        }

        /* SUMMARY CARDS */

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .dashboard-card {
            background: #fff;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            border: 1px solid #edf0f5;
            transition: .25s;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 17px;
        }

        .card-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .icon-blue {
            background: #e7f0ff;
            color: #2875d7;
        }

        .icon-gold {
            background: #fff7d6;
            color: #b59600;
        }

        .icon-green {
            background: #e3f7ed;
            color: #26975a;
        }

        .icon-purple {
            background: #f0eaff;
            color: #7852c8;
        }

        .card-label {
            font-size: 12px;
            color: #888;
            margin-bottom: 5px;
        }

        .card-number {
            font-size: 27px;
            font-weight: 700;
            color: #003366;
        }

        .card-description {
            font-size: 10px;
            color: #999;
            margin-top: 7px;
        }

        /* CONTENT GRID */

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 22px;
            margin-bottom: 25px;
        }

        .content-box {
            background: #fff;
            border-radius: 12px;
            padding: 23px;
            border: 1px solid #edf0f5;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .04);
            min-width: 0;
        }

        .box-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }

        .box-heading h3 {
            font-size: 15px;
            color: #003366;
        }

        .view-all {
            color: #b59600;
            font-size: 11px;
            font-weight: 600;
        }

        /* WEEKLY ACTIVITIES */

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f0f2f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-date {
            min-width: 52px;
            background: #edf3fa;
            border-radius: 8px;
            padding: 8px 5px;
            text-align: center;
            color: #003366;
        }

        .activity-date strong {
            display: block;
            font-size: 17px;
        }

        .activity-date small {
            font-size: 9px;
            text-transform: uppercase;
        }

        .activity-details {
            flex: 1;
        }

        .activity-details h4 {
            color: #34465c;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .activity-details p {
            color: #999;
            font-size: 10px;
        }

        .activity-status {
            background: #e3f7ed;
            color: #26975a;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 9px;
            white-space: nowrap;
        }

        /* NOTIFICATIONS */

        .notification-item {
            display: flex;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px solid #f0f2f6;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon {
            width: 35px;
            height: 35px;
            min-width: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff7d6;
            color: #b59600;
            border-radius: 50%;
        }

        .notification-text h4 {
            font-size: 11px;
            color: #34465c;
            margin-bottom: 4px;
        }

        .notification-text p {
            font-size: 10px;
            color: #999;
        }

        /* DUTY TABLE */

        .table-responsive {
            overflow-x: auto;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 500px;
        }

        .dashboard-table th {
            background: #f6f8fb;
            color: #66758a;
            font-size: 10px;
            text-align: left;
            padding: 13px;
            font-weight: 600;
        }

        .dashboard-table td {
            padding: 14px 13px;
            font-size: 11px;
            color: #555;
            border-bottom: 1px solid #f0f2f6;
        }

        .status-badge {
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 9px;
        }

        .status-pending {
            background: #fff4d6;
            color: #a77b00;
        }

        .status-approved {
            background: #e3f7ed;
            color: #26975a;
        }

        .status-rejected {
            background: #fde8e8;
            color: #d33;
        }

        .resource-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }

        .resource-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            color: #34465c;
            font-size: 12px;
            font-weight: 600;
        }

        .resource-field input,
        .resource-field select,
        .resource-field textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #dce2ea;
            border-radius: 7px;
            background: #fff;
            color: #333;
            font: inherit;
        }

        .resource-file-picker {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 36px;
            font-weight: 400;
        }

        .resource-file-picker input[type="file"] {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            clip-path: inset(50%);
        }

        .resource-file-picker input[type="file"]:focus-visible + label {
            outline: 2px solid #3373ad;
            outline-offset: 2px;
        }

        .resource-file-picker label {
            flex: 0 0 auto;
            cursor: pointer;
        }

        .resource-file-picker span {
            overflow-wrap: anywhere;
        }

        .dashboard-action {
            display: inline-block;
            padding: 8px 12px;
            border: 0;
            border-radius: 6px;
            background: #003366;
            color: #fff;
            font-size: 11px;
            cursor: pointer;
        }

        .dashboard-action-secondary {
            background: #e7f0ff;
            color: #003366;
        }

        .dashboard-action-danger {
            margin-left: 5px;
            background: #fde8e8;
            color: #a61b1b;
        }

        .form-error {
            color: #b42318;
            font-weight: 400;
        }

        .dashboard-alert {
            margin-bottom: 18px;
            padding: 12px 16px;
            border-radius: 7px;
            background: #e3f7ed;
            color: #17633b;
        }

        .swal2-container {
            z-index: 2000 !important;
        }

        .resource-edit-form {
            min-width: 280px;
            max-width: 520px;
            padding: 16px 0;
        }

        .resource-row-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .resource-row-actions .dashboard-action-danger {
            margin-left: 0;
        }

        .resource-delete-form {
            display: inline-flex;
            margin: 0;
        }

        details summary {
            list-style: none;
        }

        details summary::-webkit-details-marker {
            display: none;
        }

        /* RESPONSIVE */

        .sidebar-overlay {
            display: none;
        }

        @media(max-width: 1100px) {
            .dashboard-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media(max-width: 850px) {
            .sidebar {
                z-index: 1301;
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .4);
                z-index: 1300;
            }

            .main-area {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 600px) {
            .topbar {
                padding: 0 16px;
                height: 65px;
            }

            .topbar-right {
                gap: 14px;
            }

            .profile-info {
                display: none;
            }

            .dashboard-content {
                padding: 25px 15px;
            }

            .dashboard-heading h1 {
                font-size: 20px;
            }

            .dashboard-cards {
                gap: 12px;
            }

            .dashboard-card {
                padding: 16px;
            }

            .card-number {
                font-size: 23px;
            }

            .card-label {
                font-size: 10px;
            }

            .content-box {
                padding: 16px;
            }
        }

        .dashboard-content {
            max-width: 1700px;
            padding: 34px clamp(18px, 3vw, 48px);
        }

        .topbar {
            border-bottom: 1px solid #e8edf4;
            backdrop-filter: blur(12px);
        }

        .sidebar {
            box-shadow: 12px 0 34px rgba(0, 27, 55, .12);
        }

        .sidebar-menu a {
            min-height: 44px;
        }

        .sidebar-menu a.active {
            box-shadow: inset 3px 0 #fff;
        }

        .dashboard-heading {
            margin-bottom: 22px;
        }

        .dashboard-heading h1 {
            font-size: clamp(24px, 2.3vw, 32px);
            letter-spacing: -.5px;
        }

        .dashboard-heading p {
            font-size: 13px;
        }

        .date-display {
            padding: 12px 16px;
            border: 1px solid #e9edf3;
            font-weight: 600;
        }

        .dashboard-welcome {
            position: relative;
            align-items: center;
            overflow: hidden;
            min-height: 180px;
            margin-bottom: 24px;
            padding: 30px clamp(22px, 3vw, 40px);
            border-radius: 17px;
            background: linear-gradient(115deg, #003366, #145986);
            color: #fff;
            box-shadow: 0 12px 28px rgba(0, 51, 102, .16);
        }

        .dashboard-welcome::after {
            position: absolute;
            top: -110px;
            right: 12%;
            width: 280px;
            height: 280px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 50%;
            box-shadow: 0 0 0 36px rgba(255, 255, 255, .035), 0 0 0 72px rgba(255, 255, 255, .025);
            content: "";
        }

        .dashboard-welcome-copy,
        .dashboard-welcome-meta {
            position: relative;
            z-index: 1;
        }

        .dashboard-welcome-copy h1 {
            color: #fff;
        }

        .dashboard-welcome-copy p {
            color: rgba(255, 255, 255, .78);
        }

        .dashboard-welcome-meta {
            display: grid;
            justify-items: end;
            gap: 10px;
        }

        .dashboard-user-chip {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 12px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 30px;
            background: rgba(255, 255, 255, .1);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
        }

        .dashboard-user-chip span {
            display: grid;
            width: 25px;
            height: 25px;
            place-items: center;
            border-radius: 50%;
            background: #f5dc57;
            color: #003366;
        }

        .dashboard-welcome .date-display {
            border: 0;
            background: rgba(255, 255, 255, .1);
            color: #fff;
            box-shadow: none;
        }

        .overview-stats {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .finance-summary-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: -6px;
        }

        .finance-summary-cards .dashboard-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 90px;
            padding: 20px 24px;
        }

        .finance-summary-cards .card-number {
            font-size: 24px;
        }

        .quick-access-card {
            min-height: 165px;
        }

        .quick-access-card .card-icon {
            margin-bottom: 15px;
        }

        .quick-access-card h2 {
            margin-bottom: 7px;
            color: #123b64;
            font-size: 15px;
        }

        .quick-access-card p {
            color: #718096;
            font-size: 11px;
            line-height: 1.6;
        }

        .dashboard-empty-state {
            display: grid;
            min-height: 210px;
            align-content: center;
            justify-items: center;
            padding: 24px;
            color: #718096;
            text-align: center;
        }

        .dashboard-empty-state>span {
            display: grid;
            width: 52px;
            height: 52px;
            margin-bottom: 13px;
            place-items: center;
            border-radius: 16px;
            background: #f0f5fa;
            color: #37658e;
            font-size: 21px;
        }

        .dashboard-empty-state h2 {
            margin-bottom: 5px;
            color: #123b64;
            font-size: 15px;
        }

        .dashboard-empty-state p {
            font-size: 12px;
        }

        .dashboard-cards {
            gap: 16px;
        }

        .dashboard-card,
        .content-box {
            border: 1px solid #e8edf4;
            box-shadow: 0 8px 26px rgba(25, 45, 75, .055);
        }

        .dashboard-card {
            border-radius: 14px;
        }

        .dashboard-card:hover {
            border-color: #d3deec;
        }

        .card-label {
            font-size: 12px;
            font-weight: 600;
        }

        .card-number {
            font-size: 30px;
        }

        .content-box {
            border-radius: 14px;
        }

        .box-heading {
            padding-bottom: 13px;
            border-bottom: 1px solid #edf0f5;
        }

        .box-heading h3 {
            font-size: 15px;
        }

        .view-all {
            padding: 7px 11px;
            border-radius: 6px;
            background: #fff8d9;
        }

        .activity-item,
        .notification-item {
            padding: 16px 0;
        }

        .activity-details h4,
        .notification-text h4 {
            font-size: 13px;
        }

        .activity-details p,
        .notification-text p {
            margin-top: 4px;
            font-size: 11px;
            line-height: 1.6;
        }

        .dashboard-table {
            min-width: 640px;
        }

        .dashboard-table th {
            padding: 15px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .45px;
        }

        .dashboard-table td {
            padding: 15px;
            font-size: 12px;
            vertical-align: middle;
        }

        .dashboard-table tbody tr:hover {
            background: #f9fbfe;
        }

        .dashboard-table td[colspan] {
            padding: 28px 15px;
            color: #78869a;
        }

        .resource-page-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .resource-page-heading p {
            margin-top: 6px;
            color: #78869a;
            font-size: 13px;
        }

        .resource-panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            color: #123b64;
            font-size: 16px;
        }

        .resource-panel-title i {
            color: #b59600;
        }

        .resource-form-grid {
            gap: 18px;
        }

        .resource-field {
            gap: 8px;
            font-size: 12px;
        }

        .resource-field input,
        .resource-field select,
        .resource-field textarea {
            min-height: 42px;
            border-color: #d9e1eb;
            border-radius: 8px;
            transition: border-color .2s, box-shadow .2s;
        }

        .resource-field input:focus,
        .resource-field select:focus,
        .resource-field textarea:focus {
            outline: 0;
            border-color: #3373ad;
            box-shadow: 0 0 0 3px rgba(51, 115, 173, .12);
        }

        .dashboard-action {
            min-height: 36px;
            padding: 9px 14px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            transition: transform .15s, box-shadow .15s;
        }

        .dashboard-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
        }

        .dashboard-action-secondary:hover {
            background: #dbe9fb;
        }

        .dashboard-action-danger:hover {
            background: #f9dada;
        }

        .resource-page-heading+.content-box {
            margin-bottom: 24px;
        }

        .resource-records-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .resource-records-heading h2 {
            color: #123b64;
            font-size: 16px;
        }

        .resource-records-heading span {
            color: #7d8999;
            font-size: 12px;
        }

        .resource-edit-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
            min-width: min(75vw, 700px);
            max-width: 900px;
            padding: 18px;
            border: 1px solid #e8edf4;
            border-radius: 10px;
            background: #f9fbfd;
        }

        .resource-edit-form button {
            justify-self: start;
        }

        details summary {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 18px;
            list-style: none;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 7px 11px;
            border: 1px solid #e1e7ef;
            border-radius: 6px;
            color: #123b64;
            background: #fff;
            font-size: 12px;
        }

        .pagination [aria-current="page"] span {
            border-color: #003366;
            background: #003366;
            color: #fff;
        }

        .role-hero {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            overflow: hidden;
            margin-bottom: 24px;
            padding: clamp(24px, 4vw, 42px);
            border-radius: 18px;
            background: linear-gradient(120deg, #003366, #125687);
            color: #fff;
            box-shadow: 0 12px 30px rgba(0, 51, 102, .16);
        }

        .role-hero::after {
            position: absolute;
            top: -90px;
            right: -40px;
            width: 280px;
            height: 280px;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 50%;
            box-shadow: 0 0 0 35px rgba(255, 255, 255, .04), 0 0 0 70px rgba(255, 255, 255, .035);
            content: "";
        }

        .role-hero-content {
            position: relative;
            z-index: 1;
        }

        .role-hero h1 {
            margin-bottom: 8px;
            font-size: clamp(25px, 3vw, 34px);
        }

        .role-hero p {
            max-width: 580px;
            color: rgba(255, 255, 255, .82);
            font-size: 13px;
            line-height: 1.7;
        }

        .role-hero-icon {
            position: relative;
            z-index: 1;
            display: grid;
            width: 74px;
            height: 74px;
            flex: 0 0 74px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 20px;
            background: rgba(255, 255, 255, .12);
            color: #f5dc57;
            font-size: 30px;
        }

        .role-section-title {
            margin: 0 0 16px;
            color: #123b64;
            font-size: 17px;
        }

        .role-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .role-card {
            min-height: 165px;
            padding: 22px;
            border: 1px solid #e8edf4;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(25, 45, 75, .045);
        }

        .role-card-icon {
            display: grid;
            width: 42px;
            height: 42px;
            margin-bottom: 16px;
            place-items: center;
            border-radius: 11px;
            background: #fff7d6;
            color: #9c7d00;
            font-size: 17px;
        }

        .role-card h2 {
            margin-bottom: 7px;
            color: #123b64;
            font-size: 15px;
        }

        .role-card p {
            color: #718096;
            font-size: 12px;
            line-height: 1.7;
        }

        .role-tools {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 12px;
        }

        .role-tool-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px;
            border: 1px solid #e8edf4;
            border-radius: 10px;
            background: #fff;
            color: #123b64;
            font-size: 13px;
            font-weight: 600;
            transition: border-color .2s, transform .2s;
        }

        .role-tool-link:hover {
            transform: translateY(-2px);
            border-color: #c9a900;
        }

        .role-tool-link i:first-child {
            margin-right: 9px;
            color: #b59600;
        }

        .profile-panel {
            max-width: 920px;
            margin: 0 auto;
        }

        .profile-banner {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
            padding: 24px;
            border-radius: 14px;
            background: linear-gradient(120deg, #003366, #125687);
            color: #fff;
        }

        .profile-banner-avatar {
            display: grid;
            width: 58px;
            height: 58px;
            flex: 0 0 58px;
            place-items: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, .15);
            color: #f5dc57;
            font-size: 24px;
        }

        .profile-banner h1 {
            margin-bottom: 5px;
            font-size: 20px;
        }

        .profile-banner p {
            color: rgba(255, 255, 255, .78);
            font-size: 12px;
        }

        .notification-page-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 18px 0;
            border-bottom: 1px solid #edf0f5;
        }

        .notification-page-item:last-child {
            border-bottom: 0;
        }

        .notification-page-item.is-unread {
            margin: 0 -12px;
            padding-right: 12px;
            padding-left: 12px;
            border-radius: 9px;
            background: #f7faff;
        }

        .notification-page-item .notification-text {
            flex: 1;
            min-width: 0;
        }

        .notification-page-item .notification-text p {
            margin: 5px 0;
        }

        .notification-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            background: #edf2f7;
            color: #5c6e82;
            font-size: 10px;
            font-weight: 600;
        }

        .notification-status.unread {
            background: #fff4d6;
            color: #8a6800;
        }

        @media(max-width: 600px) {
            .dashboard-content {
                padding: 20px 14px;
            }

            .role-hero {
                align-items: flex-start;
                padding: 24px 20px;
            }

            .role-hero-icon {
                width: 52px;
                height: 52px;
                flex-basis: 52px;
                border-radius: 14px;
                font-size: 22px;
            }

            .role-card {
                min-height: auto;
            }

            .resource-page-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .resource-edit-form {
                min-width: min(82vw, 360px);
            }

            .dashboard-welcome {
                align-items: flex-start;
                flex-direction: column;
                gap: 18px;
            }

            .dashboard-welcome-meta {
                justify-items: start;
            }

            .overview-stats {
                grid-template-columns: 1fr 1fr;
            }

            .finance-summary-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="dashboard-shell">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">

            <div class="sidebar-brand">
                <img class="brand-logo" src="{{ asset('Images/images.jpg') }}" alt="PHM HCC logo">
                <div>
                    <h2>PHM HCC</h2>
                </div>
            </div>

            <p class="sidebar-label">{{ __('Main Menu') }}</p>

            <ul class="sidebar-menu">

                <li>
                    <a href="{{ url('/dashboard') }}"
                        class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-house"></i>
                        {{ __('Dashboard') }}
                    </a>
                </li>

                @if(auth()->user()->hasPermissionTo('users.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'users') }}"
                        class="{{ request()->is('dashboard/users*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        {{ __('User Management') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('events.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'events') }}"
                        class="{{ request()->is('dashboard/events*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-days"></i>
                        {{ __('Weekly Activities') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('announcements.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'announcements') }}"
                        class="{{ request()->is('dashboard/announcements*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn"></i>
                        {{ __('Announcements') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('duties.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'duties') }}"
                        class="{{ request()->is('dashboard/duties*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        {{ __('Duty Schedule') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('meetings.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'meetings') }}"
                        class="{{ request()->is('dashboard/meetings*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        {{ __('Meetings') }}
                    </a>
                </li>
                @endif

            </ul>

            <p class="sidebar-label">{{ __('Management') }}</p>

            <ul class="sidebar-menu">

                @if(auth()->user()->hasPermissionTo('finance.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'finance') }}"
                        class="{{ request()->is('dashboard/finance*') ? 'active' : '' }}">
                        <i class="fas fa-wallet"></i>
                        {{ __('Financial Statements') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('departments.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'departments') }}"
                        class="{{ request()->is('dashboard/departments*') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        {{ __('Departments') }}
                    </a>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('reports.view'))
                <li>
                    <a href="{{ route('dashboard.resource.index', 'reports') }}"
                        class="{{ request()->is('dashboard/reports*') ? 'active' : '' }}">
                        <i class="fas fa-file-lines"></i>
                        {{ __('Reports') }}
                    </a>
                </li>
                @endif


            </ul>
        </aside>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- MAIN CONTENT -->
        <div class="main-area">

            <!-- TOPBAR -->
            <header class="topbar">

                <div class="topbar-left">

                    <button class="menu-toggle" id="menuToggle"
                        type="button" aria-label="{{ __('Toggle navigation') }}">
                        <i class="fas fa-bars"></i>
                    </button>

                </div>

                <div class="topbar-right">

                    @include('layouts.partials.language-switch', ['id' => 'dashboard-language-switch'])

                    <a href="{{ route('dashboard.notifications') }}"
                        class="topbar-icon" title="{{ __('Notifications') }}">
                        <i class="far fa-bell"></i>
                        @if(auth()->user()->unreadNotifications()->exists())
                        <span class="notification-dot"></span>
                        @endif
                    </a>

                    <div class="profile">
                        <button class="profile-toggle" id="profileToggle" type="button" aria-expanded="false" aria-controls="profileMenu" aria-label="{{ __('Open account menu') }}">
                            <span class="profile-avatar">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </span>
                            <span class="profile-info">
                                <strong>{{ auth()->user()->name ?? __('Church Leader') }}</strong>
                                <small>{{ __(str(auth()->user()->role)->replace('_', ' ')->title()->toString()) }}</small>
                            </span>
                            <i class="fas fa-chevron-down profile-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="profile-menu" id="profileMenu">
                            <a href="{{ route('dashboard.profile') }}">
                                <i class="fas fa-user-circle" aria-hidden="true"></i> {{ __('My Profile') }}
                            </a>
                            <a href="{{ route('home') }}">
                                <i class="fas fa-globe" aria-hidden="true"></i> {{ __('Visit Website') }}
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit">
                                    <i class="fas fa-right-from-bracket" aria-hidden="true"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </header>

            <!-- PAGE CONTENT -->
            <main class="dashboard-content">

                @include('layouts.partials.breadcrumbs')

                @if(session('status'))
                <div class="dashboard-alert" role="status">{{ session('status') }}</div>
                @endif

                @yield('content')

            </main>

            <footer class="dashboard-footer">
                <p>&copy; {{ date('Y') }} PHM HCC. {{ __('All Rights Reserved.') }}</p>
            </footer>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script
        data-delete-warning="{{ __('This action cannot be undone.') }}"
        data-delete-question="{{ __('Delete this record?') }}"
        data-delete-confirm="{{ __('Yes, delete it') }}"
        data-delete-cancel="{{ __('Cancel') }}"
        data-success-title="{{ __('Success') }}"
        data-ok="{{ __('OK') }}"
        data-no-file-chosen="{{ __('No file chosen') }}"
    >
        const script = document.currentScript;
        const deleteWarning = script.dataset.deleteWarning;
        const deleteQuestion = script.dataset.deleteQuestion;
        document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                if (typeof Swal === 'undefined') {
                    if (window.confirm(deleteQuestion + "\n" + deleteWarning)) {
                        form.submit();
                    }

                    return;
                }

                Swal.fire({
                    title: deleteQuestion,
                    text: deleteWarning,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#b42318',
                    cancelButtonColor: '#66758a',
                    confirmButtonText: script.dataset.deleteConfirm,
                    cancelButtonText: script.dataset.deleteCancel,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        const statusMessage = document.querySelector('.dashboard-alert')?.textContent.trim();
        if (statusMessage && typeof Swal !== 'undefined') {
            Swal.fire({
                title: script.dataset.successTitle,
                text: statusMessage,
                icon: 'success',
                confirmButtonColor: '#003366',
                confirmButtonText: script.dataset.ok,
            });
        }

        document.querySelectorAll('.resource-file-picker input[type="file"]').forEach((input) => {
            input.addEventListener('change', () => {
                const fileName = input.files?.[0]?.name;
                input.parentElement.querySelector('[data-file-name]').textContent = fileName || script.dataset.noFileChosen;
            });
        });

        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        menuToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        const profileToggle = document.getElementById('profileToggle');
        const profileMenu = document.getElementById('profileMenu');

        profileToggle.addEventListener('click', () => {
            const isOpen = profileToggle.getAttribute('aria-expanded') === 'true';
            profileToggle.setAttribute('aria-expanded', String(!isOpen));
            profileMenu.classList.toggle('open', !isOpen);
        });

        document.addEventListener('click', (event) => {
            if (!profileToggle.contains(event.target) && !profileMenu.contains(event.target)) {
                profileToggle.setAttribute('aria-expanded', 'false');
                profileMenu.classList.remove('open');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                profileToggle.setAttribute('aria-expanded', 'false');
                profileMenu.classList.remove('open');
                profileToggle.focus();
            }
        });
    </script>

    @stack('scripts')

</body>

</html>