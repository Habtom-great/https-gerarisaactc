<?php
include('header_loggedin.php');

// Define all questions and correct answers


// Define all 30 questions and answers
$questions = [
    1 => ["Which of the following is not a core financial statement?", 
        ["a" => "Income Statement", "b" => "Statement of Cash Flows", "c" => "Trial Balance", "d" => "Balance Sheet"], 
        "c"],
    2 => ["What is the accounting equation?", 
        ["a" => "Assets = Liabilities + Equity", "b" => "Assets + Liabilities = Equity", "c" => "Assets = Revenue - Expenses", "d" => "None of the above"], 
        "a"],
    3 => ["What type of account is unearned revenue?", 
        ["a" => "Liability", "b" => "Asset", "c" => "Revenue", "d" => "Expense"], 
        "a"],
    4 => ["Which of the following is a temporary account?", 
        ["a" => "Cash", "b" => "Retained Earnings", "c" => "Revenue", "d" => "Accounts Receivable"], 
        "c"],
    5 => ["What does GAAP stand for?", 
        ["a" => "Generally Accepted Accounting Policies", "b" => "Generally Accepted Accounting Principles", "c" => "Government Authorized Accounting Procedures", "d" => "None of the above"], 
        "b"],
    6 => ["Which method is used to calculate depreciation of an asset over time?", 
        ["a" => "FIFO", "b" => "Straight-Line Method", "c" => "Double Declining Balance", "d" => "Both B and C"], 
        "d"],
    7 => ["What is a ledger?", 
        ["a" => "A summary of transactions", "b" => "A book where financial transactions are recorded", "c" => "A type of report", "d" => "None of the above"], 
        "b"],
    8 => ["Which of these accounts has a normal debit balance?", 
        ["a" => "Accounts Payable", "b" => "Revenue", "c" => "Equipment", "d" => "Unearned Revenue"], 
        "c"],
    9 => ["Which financial statement shows the financial position of a company at a specific point in time?", 
        ["a" => "Balance Sheet", "b" => "Income Statement", "c" => "Cash Flow Statement", "d" => "Trial Balance"], 
        "a"],
    10 => ["What is the primary purpose of the trial balance?", 
        ["a" => "To check the equality of debits and credits", "b" => "To prepare financial statements", "c" => "To calculate net income", "d" => "None of the above"], 
        "a"],
    11 => ["Which inventory method results in the highest net income during inflation?", 
        ["a" => "FIFO", "b" => "LIFO", "c" => "Weighted Average", "d" => "Specific Identification"], 
        "a"],
    12 => ["Which account is increased with a credit entry?", 
        ["a" => "Cash", "b" => "Revenue", "c" => "Expenses", "d" => "Dividends"], 
        "b"],
    13 => ["Which of the following is not an adjusting entry?", 
        ["a" => "Prepaid expenses", "b" => "Accrued revenues", "c" => "Post-closing entries", "d" => "Depreciation"], 
        "c"],
    14 => ["What is the formula for calculating gross profit?", 
        ["a" => "Revenue - Expenses", "b" => "Revenue - Cost of Goods Sold", "c" => "Net Income - Expenses", "d" => "Revenue + Cost of Goods Sold"], 
        "b"],
    15 => ["Which of the following is considered an intangible asset?", 
        ["a" => "Land", "b" => "Building", "c" => "Goodwill", "d" => "Inventory"], 
        "c"],
    16 => ["What is the purpose of an audit?", 
        ["a" => "To ensure tax compliance", "b" => "To review financial statements for accuracy", "c" => "To calculate net income", "d" => "To prepare balance sheets"], 
        "b"],
    17 => ["What type of account is accumulated depreciation?", 
        ["a" => "Expense", "b" => "Liability", "c" => "Contra Asset", "d" => "Equity"], 
        "c"],
    18 => ["Which of these is a financing activity in the cash flow statement?", 
        ["a" => "Payment of dividends", "b" => "Purchase of inventory", "c" => "Interest received", "d" => "Payment to suppliers"], 
        "a"],
    19 => ["Which type of account is owner's equity?", 
        ["a" => "Asset", "b" => "Liability", "c" => "Equity", "d" => "Revenue"], 
        "c"],
    20 => ["What is the main goal of managerial accounting?", 
        ["a" => "To prepare tax returns", "b" => "To provide information for internal decision-making", "c" => "To create external reports", "d" => "To track inventory"], 
        "b"],
    21 => ["Which ratio measures a company's ability to pay short-term liabilities?", 
        ["a" => "Current Ratio", "b" => "Debt-to-Equity Ratio", "c" => "Net Profit Margin", "d" => "Return on Equity"], 
        "a"],
    22 => ["What is goodwill?", 
        ["a" => "A tangible asset", "b" => "An intangible asset resulting from acquiring another business", "c" => "Revenue from selling inventory", "d" => "None of the above"], 
        "b"],
    23 => ["What does the statement of cash flows measure?", 
        ["a" => "Profitability", "b" => "Cash inflows and outflows", "c" => "Assets and liabilities", "d" => "Revenues"], 
        "b"],
    24 => ["What is the meaning of retained earnings?", 
        ["a" => "Revenue minus expenses", "b" => "Cumulative net income not distributed as dividends", "c" => "Total revenue minus dividends", "d" => "Equity minus liabilities"], 
        "b"],
    25 => ["Which of the following is a current liability?", 
        ["a" => "Accounts Payable", "b" => "Equipment", "c" => "Goodwill", "d" => "Long-term debt"], 
        "a"],
    26 => ["What is the main focus of financial accounting?", 
        ["a" => "Internal decision-making", "b" => "Creating external reports for stakeholders", "c" => "Preparing tax returns", "d" => "Managing cash flows"], 
        "b"],
    27 => ["Which type of business organization is owned by shareholders?", 
        ["a" => "Sole Proprietorship", "b" => "Partnership", "c" => "Corporation", "d" => "Cooperative"], 
        "c"],
    28 => ["What is a contingent liability?", 
        ["a" => "A liability with a known amount and timing", "b" => "A potential liability dependent on a future event", "c" => "A liability due within one year", "d" => "A type of revenue"], 
        "b"],
    29 => ["What is the matching principle?", 
        ["a" => "Matching revenues with expenses in the same period", "b" => "Matching liabilities with assets", "c" => "Matching revenues with costs", "d" => "None of the above"], 
        "a"],
    30 => ["Which financial statement reports net income?", 
        ["a" => "Balance Sheet", "b" => "Income Statement", "c" => "Cash Flow Statement", "d" => "Retained Earnings Statement"], 
        "b"],
];


// Total number of questions
$totalQuestions = count($questions);
$questionsPerPage = 10;
$totalPages = ceil($totalQuestions / $questionsPerPage);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($currentPage < 1 || $currentPage > $totalPages) {
    $currentPage = 1;
}

$startIndex = ($currentPage - 1) * $questionsPerPage + 1;
$endIndex = min($startIndex + $questionsPerPage - 1, $totalQuestions);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['answers'])) {
        $_SESSION['answers'] = [];
    }

    foreach ($_POST as $key => $value) {
        if (is_numeric($key) && isset($questions[$key])) {
            $_SESSION['answers'][(int)$key] = $value;
        }
    }

    if ($currentPage < $totalPages) {
        header("Location: ?page=" . ($currentPage + 1));
        exit;
    } else {
        $score = 0;
        foreach ($_SESSION['answers'] as $questionNumber => $userAnswer) {
            if (isset($questions[$questionNumber]) && $questions[$questionNumber][2] === $userAnswer) {
                $score++;
            }
        }
        echo "<div class='result'><h2>Your Score: $score / $totalQuestions</h2></div>";
        session_destroy();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounting Exam</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
     
        
        .question {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background-color: #f9f9f9;
        }
        .question p {
            font-weight: bold;
        }
        .options label {
            display: block;
            margin: 8px 0;
            padding: 10px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        .options input {
            margin-right: 10px;
        }
        .options label:hover {
            background-color: #f1f1f1;
        }
        .navigation {
            text-align: center;
            margin-top: 20px;
        }
        .navigation a {
            margin: 0 10px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
        }
        .navigation a:hover {
            background-color: #0056b3;
        }
        .submit-btn {
            display: block;
            width: 30%;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .result {
            text-align: center;
            font-size: 24px;
            color: #333;
        }
    </style>
</head>
<body>
    <header>Accounting Exam</header>
    <div class="container">
        <form method="post" action="">
            <?php
            for ($i = $startIndex; $i <= $endIndex; $i++) {
                if (isset($questions[$i])) {
                    $question = $questions[$i];
                    echo "<div class='question'><p>$i. {$question[0]}</p><div class='options'>";
                    foreach ($question[1] as $key => $option) {
                        echo "<label><input type='radio' name='$i' value='$key'> $key. $option</label>";
                    }
                    echo "</div></div>";
                }
            }
            ?>
            <div class="navigation">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
            <?php endif; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>">Next</a>
            <?php endif; ?>
             <button type="submit" class="submit-btn">Submit</button>
        </div>
    </div>
           
        </form>
        
</body>
</html>


