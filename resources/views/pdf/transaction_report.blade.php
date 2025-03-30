<!DOCTYPE html>
<html>
<head>
    <title>Transaction Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-algin:centre;">Transaction Report</h2>
    <p>Account Name:{{$user->account_name}}<p>
    <p>Account Number:{{$user->account_number}}<p>
    <p>Balance:{{$user->currency}} {{$user->balance}}<p>
    <p>Date Range: {{ request('from') }} - {{ request('to') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @php 
              {{$index=1; }}
            @endphp

            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $index }}</td>
                <td>{{ $transaction->description }}</td>
                <td>{{ $transaction->type=='Credit'? $user->currency.' '.$transaction->amount:'' }}</td>
                <td>{{ $transaction->type=='Debit'? $user->currency.' '.$transaction->amount:'' }}</td>
                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @php 
              {{$index++; }}
            @endphp
            @endforeach
        </tbody>
    </table>
</body>
</html>
