<?php
// Include database connection file
include('db.php');
include('header.php');

// Define the questions array with exam types
$questions = [
    [
        'exam_type' => 'quiz',
        'question' => 'Which of the following is considered an asset?',
        'option_a' => 'Accounts payable',
        'option_b' => 'Accounts receivable',
        'option_c' => 'Common stock',
        'option_d' => 'Retained earnings',
        'answer' => 'B) Accounts receivable'
    ],
    [
        'exam_type' => 'quiz',
        'question' => 'Which financial statement provides information about a company\'s financial position at a specific point in time?',
        'option_a' => 'Income statement',
        'option_b' => 'Balance sheet',
        'option_c' => 'Statement of cash flows',
        'option_d' => 'Statement of retained earnings',
        'answer' => 'B) Balance sheet'
    ],
    [
        'exam_type' => 'midterm',
        'question' => 'What is the accounting equation?',
        'option_a' => 'Assets = Liabilities + Expenses',
        'option_b' => 'Assets = Liabilities + Revenue',
        'option_c' => 'Assets = Liabilities + Equity',
        'option_d' => 'Assets = Equity - Liabilities',
        'answer' => 'C) Assets = Liabilities + Equity'
    ],
    [
        'exam_type' => 'midterm',
        'question' => 'Which of the following is a long-term liability?',
        'option_a' => 'Accounts payable',
        'option_b' => 'Mortgage payable',
        'option_c' => 'Prepaid expenses',
        'option_d' => 'Inventory',
        'answer' => 'B) Mortgage payable'
    ],
    [
        'exam_type' => 'final',
        'question' => 'Which of the following accounts is increased with a debit?',
        'option_a' => 'Common stock',
        'option_b' => 'Accounts payable',
        'option_c' => 'Service revenue',
        'option_d' => 'Cash',
        'answer' => 'D) Cash'
    ],
    [
        'exam_type' => 'final',
        'question' => 'What is the purpose of the income statement?',
        'option_a' => 'To show the financial position of a company',
        'option_b' => 'To show the cash inflows and outflows',
        'option_c' => 'To show the profitability of a company over a specific period',
        'option_d' => 'To show the changes in equity',
        'answer' => 'C) To show the profitability of a company over a specific period'
    ],
    // Add more questions here for other types
];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Prepare the SQL statement
    $sql = "INSERT INTO exam_questions (exam_type, question, option_a, option_b, option_c, option_d, correct_answer) VALUES (:exam_type, :question, :option_a, :option_b, :option_c, :option_d, :answer)";
    $stmt = $pdo->prepare($sql);

    // Execute the statement for each question
    foreach ($questions as $question) {
        $stmt->execute($question);
    }

    // Commit the transaction
    $pdo->commit();
    echo "Questions inserted successfully.";
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    echo "Failed to insert questions: " . $e->getMessage();
}
?>


lllllllllllllllllll

<?php
// Include database connection file

include('db.php');
include('header.php');
// Define the questions array
$questions = [
    [
        'question' => 'Which of the following is considered an asset?',
        'option_a' => 'Accounts payable',
        'option_b' => 'Accounts receivable',
        'option_c' => 'Common stock',
        'option_d' => 'Retained earnings',
        'answer' => 'B) Accounts receivable'
    ],
    [
        'question' => 'Which financial statement provides information about a company\'s financial position at a specific point in time?',
        'option_a' => 'Income statement',
        'option_b' => 'Balance sheet',
        'option_c' => 'Statement of cash flows',
        'option_d' => 'Statement of retained earnings',
        'answer' => 'B) Balance sheet'
    ],
    [
        'question' => 'What is the accounting equation?',
        'option_a' => 'Assets = Liabilities + Expenses',
        'option_b' => 'Assets = Liabilities + Revenue',
        'option_c' => 'Assets = Liabilities + Equity',
        'option_d' => 'Assets = Equity - Liabilities',
        'answer' => 'C) Assets = Liabilities + Equity'
    ],
    [
        'question' => 'Which of the following is a long-term liability?',
        'option_a' => 'Accounts payable',
        'option_b' => 'Mortgage payable',
        'option_c' => 'Prepaid expenses',
        'option_d' => 'Inventory',
        'answer' => 'B) Mortgage payable'
    ],
    [
        'question' => 'Which of the following accounts is increased with a debit?',
        'option_a' => 'Common stock',
        'option_b' => 'Accounts payable',
        'option_c' => 'Service revenue',
        'option_d' => 'Cash',
        'answer' => 'D) Cash'
    ],
    [
        'question' => 'What is the purpose of the income statement?',
        'option_a' => 'To show the financial position of a company',
        'option_b' => 'To show the cash inflows and outflows',
        'option_c' => 'To show the profitability of a company over a specific period',
        'option_d' => 'To show the changes in equity',
        'answer' => 'C) To show the profitability of a company over a specific period'
    ],
    [
        'question' => 'Which of the following is considered an expense?',
        'option_a' => 'Dividends',
        'option_b' => 'Accounts receivable',
        'option_c' => 'Wages expense',
        'option_d' => 'Equipment',
        'answer' => 'C) Wages expense'
    ],
    [
        'question' => 'What does GAAP stand for?',
        'option_a' => 'Generally Accepted Accounting Principles',
        'option_b' => 'General Accounting and Auditing Procedures',
        'option_c' => 'Governmental Accounting and Auditing Practices',
        'option_d' => 'Generally Approved Accounting Practices',
        'answer' => 'A) Generally Accepted Accounting Principles'
    ],
    [
        'question' => 'What is the primary purpose of a trial balance?',
        'option_a' => 'To show the financial position of a company',
        'option_b' => 'To ensure that debits equal credits in the ledger',
        'option_c' => 'To prepare the income statement',
        'option_d' => 'To show the cash inflows and outflows',
        'answer' => 'B) To ensure that debits equal credits in the ledger'
    ],
    [
        'question' => 'Which of the following transactions would increase owner\'s equity?',
        'option_a' => 'Payment of dividends',
        'option_b' => 'Owner\'s investment',
        'option_c' => 'Purchase of equipment',
        'option_d' => 'Payment of rent expense',
        'answer' => 'B) Owner\'s investment'
    ],
];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Prepare the SQL statement
    $sql = "INSERT INTO exam_questions (question, option_a, option_b, option_c, option_d, correct_answer) VALUES (:question, :option_a, :option_b, :option_c, :option_d, :answer)";
    $stmt = $pdo->prepare($sql);

    // Execute the statement for each question
    foreach ($questions as $question) {
        $stmt->execute($question);
    }

    // Commit the transaction
    $pdo->commit();
    echo "Questions inserted successfully.";
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    echo "Failed to insert questions: " . $e->getMessage();
}
?>
