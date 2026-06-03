<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Counter</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: ui-monospace, 'Cascadia Code', monospace;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            gap: 2rem;
        }

        .label {
            font-size: 0.875rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #64748b;
        }

        .count {
            font-size: clamp(4rem, 20vw, 10rem);
            font-weight: 700;
            color: #38bdf8;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            transition: color 0.15s;
        }

        .count.flash { color: #f0abfc; }

        form button {
            padding: 0.6rem 2rem;
            background: transparent;
            border: 1px solid #334155;
            border-radius: 6px;
            color: #94a3b8;
            font-family: inherit;
            font-size: 0.875rem;
            cursor: pointer;
            letter-spacing: 0.05em;
            transition: border-color 0.15s, color 0.15s;
        }

        form button:hover {
            border-color: #f87171;
            color: #f87171;
        }

        .refresh-hint {
            font-size: 0.75rem;
            color: #334155;
        }
    </style>
</head>
<body>
    <span class="label">requests handled</span>
    <div class="count" id="count">{{ $count }}</div>

    <form method="POST" action="/reset">
        @csrf
        <button type="submit">reset</button>
    </form>

</body>
</html>
