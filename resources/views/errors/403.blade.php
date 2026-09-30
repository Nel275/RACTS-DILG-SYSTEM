<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied | RACTS</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="text-center px-3">
        <div class="error-code">403</div>
        <h1 class="h4 mb-3">Access Denied</h1>
        <p class="text-muted mb-4">
            {{ $exception->getMessage() ?: 'You are not authorized to access this page.' }}
        </p>
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary">
            {{ auth()->check() ? 'Back to Dashboard' : 'Go to Login' }}
        </a>
    </div>
</body>
</html>
