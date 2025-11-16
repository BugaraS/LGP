<?php
session_start();
include '../conn.php';

// Only allow logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ========== LANGUAGE SETUP ==========
$lang = $_SESSION['lang'] ?? 'en';

$texts = [
    'en' => [
        'dashboard' => 'Admin Dashboard',
        'staff' => 'Staff',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Gate Pass System',
        'add_new' => 'Add New Staff',
        'search_placeholder' => 'Search...',
        'reset' => 'Reset',
        'export' => 'Export',
        'delete_selected' => 'Delete Selected',
        'id' => 'ID',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College',
        'position' => 'Position',
        'photo' => 'Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Serial',
        'action' => 'Action',
        'language' => 'Language',
        'rights' => 'All rights reserved.',
    ],
    'am' => [
        'dashboard' => 'የአስተዳዳሪ ዳሽ_ቦርድ',
        'staff' => 'ሰራተኞች',
        'logout' => 'ውጣ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ በር ማለፊያ ስርዓት',
        'add_new' => 'አዲስ ሰራተኛ ይመዝግቡ',
        'search_placeholder' => 'ፈልግ...',
        'reset' => 'እንደገና ይጀምሩ',
        'export' => 'ወደ ኤክሴል ላክ',
        'delete_selected' => 'የተመረጡን አጥፋ',
        'id' => 'መለያ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ',
        'position' => 'ስራ',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'action' => 'እርምጃ',
        'language' => 'ቋንቋ',
        'rights' => 'መብቱ በህግ የተጠበቀ ነው።',
    ]
];

$t = $texts[$lang];

// ================= Delete Selected Records =================
if (isset($_POST['delete_selected']) && !empty($_POST['ids'])) {
    $ids = implode(",", array_map('intval', $_POST['ids']));
    $conn->query("DELETE FROM staff WHERE id IN ($ids)");
    header("Location: Staff_Registrations.php");
    exit;
}

// ================= Delete Single Record =================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM staff WHERE id=$id");
    header("Location: Staff_Registrations.php");
    exit;
}

// ================= Export Excel =================
if (isset($_POST['export_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=registrations.xls");

    $result = $conn->query("SELECT * FROM staff");

    echo $t['id']."\t".$t['first_name']."\t".$t['middle_name']."\t".$t['last_name']."\t".$t['college']."\t".$t['position']."\t".$t['laptop_type']."\t".$t['laptop_serial']."\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['id']."\t".$row['fname']."\t".$row['mname']."\t".$row['lname']."\t".$row['college']."\t".$row['position']."\t".$row['laptop_type']."\t".$row['laptop_serial']."\n";
    }
    exit;
}

// ================= Search & Pagination =================
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$where = '';
if ($search !== '') {
    $where = "WHERE id LIKE '%$search%' OR fname LIKE '%$search%' OR mname LIKE '%$search%' OR lname LIKE '%$search%'";
}

$total_result = $conn->query("SELECT COUNT(*) AS total FROM staff $where");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

$result = $conn->query("SELECT * FROM staff $where ORDER BY id DESC LIMIT $offset, $limit");
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['staff'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../Staff_registarions_style.css">
<link rel="stylesheet" href="../Language_Style.css">
<script>
function toggleCheckboxes(master) {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = master.checked);
}
</script>
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <h2>DBULGP</h2>
        <a href="Admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a href="Staff_Registrations.php" class="active"><i class="fas fa-users"></i> <?= $t['staff'] ?></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?></a>
    </div>

    <div class="main">
        <header>
            <h2><?= $t['system_title'] ?></h2>
            <div class="language-switcher">
                <span><?= $t['language'] ?>:</span>
                <a href="language_switch.php?lang=en">English</a>
                <a href="language_switch.php?lang=am">አማርኛ</a>
            </div>
        </header>

        <div class="content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="top-left">
                    <a href="Staff_register.php" class="add-new"><?= $t['add_new'] ?></a>
                </div>

                <div class="top-right">
                    <form method="get" style="display:flex; gap:5px;">
                        <input type="text" name="search" placeholder="<?= $t['search_placeholder'] ?>" value="<?= $search ?>">
                        <button type="submit"><?= $t['search_placeholder'] ?></button>
                        <a href="Staff_Registrations.php" class="reset"><?= $t['reset'] ?></a>
                    </form>

                    <form method="post">
                        <button type="submit" name="export_excel"><?= $t['export'] ?></button>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <form method="post">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
                            <th><?= $t['id'] ?></th>
                            <th><?= $t['first_name'] ?></th>
                            <th><?= $t['middle_name'] ?></th>
                            <th><?= $t['last_name'] ?></th>
                            <th><?= $t['college'] ?></th>
                            <th><?= $t['position'] ?></th>
                            <th><?= $t['photo'] ?></th>
                            <th><?= $t['laptop_type'] ?></th>
                            <th><?= $t['laptop_serial'] ?></th>
                            <th><?= $t['action'] ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['fname']) ?></td>
                                    <td><?= htmlspecialchars($row['mname']) ?></td>
                                    <td><?= htmlspecialchars($row['lname']) ?></td>
                                    <td><?= htmlspecialchars($row['college']) ?></td>
                                    <td><?= htmlspecialchars($row['position']) ?></td>
                                    <td>
                                        <?php if (!empty($row['photo']) && file_exists("uploads/".$row['photo'])): ?>
                                            <img src="uploads/<?= $row['photo'] ?>" alt="photo" width="50">
                                        <?php else: ?> No Photo <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['laptop_type']) ?></td>
                                    <td><?= htmlspecialchars($row['laptop_serial']) ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-dots"><i class="fas fa-ellipsis-v"></i></button>
                                            <div class="action-menu">
                                                <a href="Edit_Staff.php?id=<?= $row['id'] ?>">Edit</a>
                                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this staff?')">Delete</a>
                                                <a href="Generate_staff_LGP_id.php?id=<?= $row['id'] ?>" target="_blank">Generate LGP ID</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="11">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="submit" name="delete_selected"><?= $t['delete_selected'] ?></button>
            </form>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&search=<?= $search ?>">Prev</a>
                <?php endif; ?>
                <?php for ($i=1; $i<=$total_pages; $i++): ?>
                    <?php if ($i >= $page-2 && $i <= $page+2): ?>
                        <a href="?page=<?= $i ?>&search=<?= $search ?>" class="<?= ($i==$page)?'active':'' ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page+1 ?>&search=<?= $search ?>">Next</a>
                <?php endif; ?>
            </div>

        </div>

        <footer>
            &copy; 2025 DBU. <?= $t['rights'] ?>
        </footer>
    </div>
</div>
</body>
</html>
