<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order {{ $orderId }} Project Data</title>
</head>
<body>
    <h2>Order ID: {{ $orderId }}</h2>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Field Name</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($project_data as $field)
            <tr>
                <td>{{ $field->field_name }}</td>
                <td>
                    @if($field->field_type == 'file_upload')
                        <a href="{{ asset('storage/' . $field->field_value) }}" target="_blank">{{ $field->field_value }}</a>
                    @else
                        {{ $field->field_value }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
