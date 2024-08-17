<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input</title>
</head>

<body>
    <h1>Enter Start Date</h1>
    <form id="dateForm">
        <label for="startDate">Start Date:</label>
        <input type="text" id="startDate" name="startDate" placeholder="e.g., 1st Jan 2024" required>
        <button type="submit">Submit</button>
    </form>

    <div id="result"></div>

    <script>
        document.getElementById('dateForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var startDate = document.getElementById('startDate').value;

            var apiUrl = 'http://localhost:3000/api/purchase-orders?start_date=' + encodeURIComponent(startDate);

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('result').innerHTML = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>

/* if ($startDate->day == 1) {
$cycleEndDate = $cycleStartDate->copy()->endOfMonth();
} else {
$cycleEndDate = $cycleStartDate->copy()->addMonth()->subDay();
} */
