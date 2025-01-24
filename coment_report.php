<?php
// Start session and include necessary files
include('db.php');
include('header.php');
session_start()
// Check if the user is logged in and has the role of 'admin'

?>
<div class="admin-comments">
        <h1>Comments Report</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Video ID</th>
                    <th>User Name</th>
                    <th>Comment</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><?= htmlspecialchars($comment['user_id']); ?></td>
                            <td><?= htmlspecialchars($comment['video_id']); ?></td>
                            <td><?= htmlspecialchars($comment['user_name']); ?></td>
                            <td><?= htmlspecialchars($comment['comment_text']); ?></td>
                            <td><?= htmlspecialchars($comment['created_at']); ?></td>
                            <td>
                                <form action="delete-comment.php" method="post" style="display:inline;">
                                   
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No comments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('footer.php'); ?>

</body>
</html>


