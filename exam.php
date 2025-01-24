<?php
require_once 'db.php';
require_once 'header_loggedin.php';

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name']);
    $journal_entries = trim($_POST['journal_entries']);
    $ledger = trim($_POST['ledger']);
    $fixed_asset_schedule = trim($_POST['fixed_asset_schedule']);
    $financial_statements = trim($_POST['financial_statements']);

    $sql = "INSERT INTO exam_answers (student_name, journal_entries, ledger, fixed_asset_schedule, financial_statements) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $student_name, $journal_entries, $ledger, $fixed_asset_schedule, $financial_statements);

    if ($stmt->execute()) {
        echo "<script>alert('Your answers have been submitted successfully!');</script>";
    } else {
        echo "<script>alert('There was an error submitting your answers. Please try again.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting Journal Entry - Rahwa Legal Service</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        header, footer {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: #fff;
        }
        main {
            padding: 20px;
        }
        section {
            margin-bottom: 20px;
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table.journal-entry {
            width: 100%;
            height: 100%;
            margin: 0;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.journal-entry {
            width: 100%;
            height: 100%;
            margin: 0;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.journal-entry th,[type="number"] {
           
            border: 1px solid lightgrey;
            text-align: center;
            width: 10%;
            padding: 5px;
        }
        
        table.journal-entry td {
            padding: 15px;
            border: 1px solid lightgrey;
            text-align: center;
        }
        table.journal-entry th {
            background-color: #D6ECF3;
        }
        .input[type="number"] {
       
            width: 10%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header>
        <h1>Rahwa Legal Service Business - Accounting Exam</h1>
        <p>Month of January 2023</p>
    </header>
    <main>
        <section>
            <h2>Instructions</h2>
            <p>Answer the following questions based on the transactions provided. Use the spaces below each question to write your answers.</p>
        </section>
        <section>
            <h2>Business Transactions</h2>
            <p>The following is Rahwa Legal Service's business transactions during the month of January 2023:</p>
            <ol>
                <li>On January 1, purchased equipment on account for $3,500, payment due within the month.</li>
                <li>On January 3, invested $20,000 cash into his business from his personal account.</li>
                <li>On January 9, received $4,000 cash in advance from a customer for services not yet rendered.</li>
                <li>On January 10, provided $5,500 in services to a customer who asked to be billed for the services.</li>
                <li>On January 10, borrowed $50,000 from a bank and invested into the business.</li>
                <li>On January 12, paid a $300 utility bill with cash.</li>
                <li>On January 14, paid $1,000 cash for fire and employee insurance.</li>
                <li>On January 17, received $2,800 cash from a customer for services rendered.</li>
                <li>On January 18, paid in full, with cash, for the equipment purchase on January 1.</li>
                <li>On January 20, paid $3,600 cash in salaries to employees.</li>
                <li>On January 23, received cash payment in full from the customer on the January 10 transaction.</li>
                <li>On January 27, provided $1,200 in services to a customer who asked to be billed for the services.</li>
                <li>On January 30, purchased supplies on account for $500, payment due within three months.</li>
            </ol>
        </section>
        <section>
            <h2>Exam Questions</h2>
            <form action="exam.php" method="POST" id="examForm">
                <label for="student_name">Student Name:</label>
                <input type="text" id="student_name" name="student_name" required>

               

                <h3>Journal Entries</h3>
                <table class="journal-entry">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>DR</th>
                            <th>CR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="date" name="date[]" value="2024-12-29"></td>
                            <td><input type="text" name="account[]" placeholder="text" step="any"></td>
                            <td><input type="number" name="debit[]" placeholder="Amount" step="any"></td>
                            <td><input type="number" name="credit[]" placeholder="Amount" step="any"></td>
                       
                        <tr>
                             <td><input type="text" name="account[]" placeholder="text" step="any"></td>
                            <td><input type="text" name="account[]" placeholder="text"></td>
                            <td><input type="number" name="debit[]" placeholder="Amount" step="any"></td>
                            <td><input type="number" name="credit[]" placeholder="Amount" step="any"></td>
                        </tr>
                        <!-- You can add more rows as needed -->
                    </tbody>
                </table>

                <button type="submit">Submit</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2023 Rahwa Legal Service</p>
    </footer>
</body>
</html>
kkkkkkkkk

<table width="200" border="0">
     <tr><td colspan="5">Account Head <?echo $AccountHead ; ?> </td> </tr>


    <tr>
           <td>Dr.</td>
           <td>amount.</td>
            <td>&nbsp;</td>
           <td>Cr</td>
          <td>Amount</td>

    </tr>
     <tr>
       <td><?echo $AccountName1 ; ?></td>
       <td><?echo $Ammount1 ; ?></td>
      <td></td>
 <td><?echo $AccountName2 ; ?></td>
     <td><?echo $Ammount2 ; ?></td>

     </tr>

     <tr>
     <td>Total</td>
     <td><?echo $TotalAmount1 ; ?></td>
     <td>&nbsp;</td>
 <td>Total  </td>
 <td><?echo $TotalAmount2 ; ?></td>

     </tr>
     </table>
     <?php
    
{
    $data = [
        'debit_side.account_code AS Code',
        'GROUP_CONCAT(DISTINCT accounts.name) AS DebitAccount',
        'GROUP_CONCAT(debit_side.amount) AS DebitAmount',
        'GROUP_CONCAT(transaction_info.voucher_date) AS DebitVoucherDate',
        'GROUP_CONCAT(DISTINCT credit_accounts.name) AS CreditAccount',
        'GROUP_CONCAT(credit_side.amount) AS CreditAmount',
        'GROUP_CONCAT(transaction_info_credits.voucher_date) AS CreditVoucherDate'
    ];

    $this->db->select($data);
    $this->db->from('accounts AS debit_accounts');
    $this->db->join('debit_side', 'debit_accounts.code = debit_side.account_code', 'left');
    $this->db->join('transaction_info', 'transaction_info.transaction_id = debit_side.transaction_id_dr', 'left');
    $this->db->join('credit_side', 'debit_side.transaction_id_dr = credit_side.transaction_id_cr', 'left');
    $this->db->join('accounts AS credit_accounts', 'credit_accounts.code = credit_side.account_code', 'left');
    $this->db->join('transaction_info AS transaction_info_credits', 'transaction_info_credits.transaction_id = credit_side.transaction_id_cr', 'left');
    $this->db->group_by('debit_side.account_code');
    $this->db->order_by('debit_side.account_code', 'ASC');

    $query = $this->db->get();

    if (!$query) {
        log_message('error', $this->db->error()); // Log database error
        return [];
    }

    return $query->result_array();
}
