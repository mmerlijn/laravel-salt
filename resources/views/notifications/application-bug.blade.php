<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Application bug</title>
</head>
<body>
<h1>Application bug</h1>

<p>Er is een applicatiefout opgetreden in {{ config('app.name') }}.</p>

<p><strong>Message:</strong> {{ $error->message ?? '-' }}</p>
<p><strong>Class:</strong> {{ $error->class ?? '-' }}</p>
@if($error->solution)
    <p><strong>Solution:</strong> {{ $error->solution }}</p>
@endif

@if(!empty($error->trace))
    <h2>Trace</h2>
    <pre style="white-space: pre-wrap;">{{ $error->trace }}</pre>
@endif

<p style="margin-top: 24px;">Met vriendelijke groet,<br>{{ config('app.name') }}</p>
</body>
</html>

