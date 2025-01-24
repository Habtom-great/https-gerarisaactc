<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .input-form {
            margin-bottom: 20px;
        }
        .input-form label {
            display: block;
            margin: 10px 0 5px;
        }
        .input-form input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Accounting Records</h1>
        
        <div class="input-form">
            <h2>Enter Journal Data</h2>
            <label for="date">Date:</label>
            <input type="date" id="journalDate" placeholder="YYYY-MM-DD">

            <label for="account">Account Title & Explanation:</label>
            <input type="text" id="journalAccount" placeholder="Account Title">

            <label for="debit">Debit (Dr):</label>
            <input type="number" id="journalDebit" placeholder="Debit Amount">

            <label for="credit">Credit (Cr):</label>
            <input type="number" id="journalCredit" placeholder="Credit Amount">

            <button class="btn" onclick="addJournalEntry()">Add Journal Entry</button>
        </div>

        <h2>Journal</h2>
        <table id="journalTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account Title & Explanation</th>
                    <th>Debit (Dr)</th>
                    <th>Credit (Cr)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Journal Entries -->
            </tbody>
        </table>

        <div class="input-form">
            <h2>Enter Ledger Data</h2>
            <label for="ledgerDate">Date:</label>
            <input type="date" id="ledgerDate" placeholder="YYYY-MM-DD">

            <label for="particulars">Particulars:</label>
            <input type="text" id="ledgerParticulars" placeholder="Particulars">

            <label for="voucher">Voucher No.:</label>
            <input type="text" id="ledgerVoucher" placeholder="Voucher Number">

            <label for="debit">Debit (Dr):</label>
            <input type="number" id="ledgerDebit" placeholder="Debit Amount">

            <label for="credit">Credit (Cr):</label>
            <input type="number" id="ledgerCredit" placeholder="Credit Amount">

            <button class="btn" onclick="addLedgerEntry()">Add Ledger Entry</button>
        </div>

        <h2>Ledger</h2>
        <table id="ledgerTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th>Voucher No.</th>
                    <th>Debit (Dr)</th>
                    <th>Credit (Cr)</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <!-- Ledger Entries -->
            </tbody>
        </table>

        <div class="input-form">
            <h2>Enter Trial Balance Data</h2>
            <label for="account">Account Title:</label>
            <input type="text" id="trialAccount" placeholder="Account Title">

            <label for="debit">Debit (Dr):</label>
            <input type="number" id="trialDebit" placeholder="Debit Amount">

            <label for="credit">Credit (Cr):</label>
            <input type="number" id="trialCredit" placeholder="Credit Amount">

            <button class="btn" onclick="addTrialBalanceEntry()">Add Trial Balance Entry</button>
        </div>

        <h2>Trial Balance</h2>
        <table id="trialBalanceTable">
            <thead>
                <tr>
                    <th>Account Title</th>
                    <th>Debit (Dr)</th>
                    <th>Credit (Cr)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Trial Balance Entries -->
            </tbody>
        </table>
    </div>

    <script>
        function addJournalEntry() {
            const date = document.getElementById('journalDate').value;
            const account = document.getElementById('journalAccount').value;
            const debit = document.getElementById('journalDebit').value;
            const credit = document.getElementById('journalCredit').value;

            const journalTable = document.querySelector('#journalTable tbody');
            journalTable.innerHTML += `
                <tr>
                    <td>${date}</td>
                    <td>${account}</td>
                    <td>${debit}</td>
                    <td>${credit}</td>
                </tr>
            `;

            // Clear inputs
            document.getElementById('journalDate').value = '';
            document.getElementById('journalAccount').value = '';
            document.getElementById('journalDebit').value = '';
            document.getElementById('journalCredit').value = '';
        }

        function addLedgerEntry() {
            const date = document.getElementById('ledgerDate').value;
            const particulars = document.getElementById('ledgerParticulars').value;
            const voucher = document.getElementById('ledgerVoucher').value;
            const debit = document.getElementById('ledgerDebit').value;
            const credit = document.getElementById('ledgerCredit').value;
            const balance = debit - credit; // Simplified balance calculation

            const ledgerTable = document.querySelector('#ledgerTable tbody');
            ledgerTable.innerHTML += `
                <tr>
                    <td>${date}</td>
                    <td>${particulars}</td>
                    <td>${voucher}</td>
                    <td>${debit}</td>
                    <td>${credit}</td>
                    <td>${balance}</td>
                </tr>
            `;

            // Clear inputs
            document.getElementById('ledgerDate').value = '';
            document.getElementById('ledgerParticulars').value = '';
            document.getElementById('ledgerVoucher').value = '';
            document.getElementById('ledgerDebit').value = '';
            document.getElementById('ledgerCredit').value = '';
        }

        function addTrialBalanceEntry() {
            const account = document.getElementById('trialAccount').value;
            const debit = document.getElementById('trialDebit').value;
            const credit = document.getElementById('trialCredit').value;

            const trialBalanceTable = document.querySelector('#trialBalanceTable tbody');
            trialBalanceTable.innerHTML += `
                <tr>
                    <td>${account}</td>
                    <td>${debit}</td>
                    <td>${credit}</td>
                </tr>
            `;

            // Clear inputs
            document.getElementById('trialAccount').value = '';
            document.getElementById('trialDebit').value = '';
            document.getElementById('trialCredit').value = '';
        }
    </script>
</body>
</html>

kkkkk
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .input-form {
            margin-bottom: 20px;
        }
        .input-form label {
            display: block;
            margin: 10px 0 5px;
        }
        .input-form input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Accounting Records</h1>
        
        <div class="input-form">
            <h2>Enter Journal Data</h2>
            <label for="date">Date:</label>
            <input type="date" id="journalDate" placeholder="YYYY-MM-DD">

            <label for="account">Account Title & Explanation:</label>
            <input type="text" id="journalAccount" placeholder="Account Title">

            <label for="debit">Debit (Dr):</label>
            <input type="number" id="journalDebit" placeholder="Debit Amount">

            <label for="credit">Credit (Cr):</label>
            <input type="number" id="journalCredit" placeholder="Credit Amount">

            <button class="btn" onclick="addJournalEntry()">Add Journal Entry</button>
        </div>

        <h2>Journal</h2>
        <table id="journalTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account Title & Explanation</th>
                    <th>Debit (Dr)</th>
                    <th>Credit (Cr)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Journal Entries -->
            </tbody>
        </table>
    </div>

    <script>
        function addJournalEntry() {
            const date = document.getElementById('journalDate').value;
            const account = document.getElementById('journalAccount').value;
            const debit = document.getElementById('journalDebit').value;
            const credit = document.getElementById('journalCredit').value;

            const journalTable = document.querySelector('#journalTable tbody');

            // Add debit row
            if (debit && debit > 0) {
                journalTable.innerHTML += `
                    <tr>
                        <td>${date}</td>
                        <td>${account}</td>
                        <td>${debit}</td>
                        <td></td>
                    </tr>
                `;
            }

            // Add credit row
            if (credit && credit > 0) {
                journalTable.innerHTML += `
                    <tr>
                        <td>${date}</td>
                        <td>${account}</td>
                        <td></td>
                        <td>${credit}</td>
                    </tr>
                `;
            }

            // Clear inputs
            document.getElementById('journalDate').value = '';
            document.getElementById('journalAccount').value = '';
            document.getElementById('journalDebit').value = '';
            document.getElementById('journalCredit').value = '';
        }
    </script>
</body>
</html>
