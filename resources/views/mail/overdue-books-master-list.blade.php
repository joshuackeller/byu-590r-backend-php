Hello manager. Here is the list of overdue books and their users:

<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Due Date</th>
            <th>Book Name</th>
            <th>Late Fee</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($checkouts as $checkout)
        <tr>
            <td>{{ $checkout->user_name }}</td>
            <td>{{ $checkout->due_date }}</td>
            <td>{{ $checkout->book_name }}</td>
            <td>$5.00</td>
        </tr>
        @endforeach

    </tbody>
</table>