<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report->title }} | PHM HCC Report</title>
    <style>
        body {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 24px;
            color: #222;
            font: 15px/1.6 Arial, sans-serif;
        }

        h1 {
            margin-bottom: 8px;
            color: #003366;
        }

        h2 {
            margin: 28px 0 8px;
            color: #003366;
            font-size: 17px;
        }

        .report-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 24px;
            margin: 24px 0;
            padding: 16px;
            background: #f4f7fb;
        }

        .report-section {
            white-space: pre-wrap;
        }

        .print-controls {
            margin-bottom: 24px;
        }

        .print-button {
            padding: 10px 16px;
            border: 0;
            border-radius: 6px;
            background: #003366;
            color: #fff;
            font: inherit;
            cursor: pointer;
        }

        @media print {
            body {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .report-details {
                background: transparent;
                border: 1px solid #ccc;
            }

            .print-controls {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <button class="print-button" type="button" onclick="window.print()">Print report</button>
    </div>

    <main>
        <h1>{{ $report->title }}</h1>
        <p>Pentecostal Holiness Mission Hope Christian Church</p>

        <div class="report-details">
            <div><strong>Reporting period:</strong> {{ $report->reporting_period?->format('d M Y') ?? '—' }}</div>
            <div><strong>Status:</strong> {{ str($report->status)->replace('_', ' ')->title() }}</div>
            <div><strong>Department:</strong> {{ $report->department?->name ?? '—' }}</div>
            <div><strong>Submitted by:</strong> {{ $report->submitter?->name ?? '—' }}</div>
            <div><strong>Submitted on:</strong> {{ $report->created_at?->format('d M Y') ?? '—' }}</div>
        </div>

        @foreach([
            'Activities' => $report->activities,
            'Achievements' => $report->achievements,
            'Challenges' => $report->challenges,
            'Recommendations' => $report->recommendations,
        ] as $heading => $content)
            @if($content)
                <section>
                    <h2>{{ $heading }}</h2>
                    <div class="report-section">{{ $content }}</div>
                </section>
            @endif
        @endforeach
    </main>
</body>
</html>
