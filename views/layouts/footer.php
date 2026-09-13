    </main>

    <!-- Global & Role-Specific Scripts -->
    <script src="assets/js/main.js"></script>
    <?php if ($auth_user): ?>
        <?php if ($auth_user['role'] === 'student'): ?>
            <script src="assets/js/student.js"></script>
        <?php elseif ($auth_user['role'] === 'recruiter'): ?>
            <script src="assets/js/recruiter.js"></script>
        <?php elseif ($auth_user['role'] === 'club_exec'): ?>
            <script src="assets/js/executive.js"></script>
        <?php elseif ($auth_user['role'] === 'admin'): ?>
            <script src="assets/js/admin.js"></script>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($extra_scripts) && is_array($extra_scripts)): ?>
        <?php foreach ($extra_scripts as $script): ?>
            <script src="assets/js/<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
