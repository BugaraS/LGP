<?php
// ================= Session Check =================
session_start();
include 'conn.php';
// Only allow logged-in admins
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ================= LANGUAGE SETUP =================
$lang = $_SESSION['lang'] ?? 'en';

$texts = [
    'en' => [
        'dashboard' => 'Admin Dashboard',
        'student' => 'Students',
        'logout' => 'Logout',
        'system_title' => 'Debre Berhan University Laptop Exit System',
        'add_new' => 'Add New Student',
        'search_placeholder' => 'Search...',
        'reset' => 'Reset',
        'export' => 'Export',
        'delete_selected' => 'Delete Selected',
        'id' => 'ID',
        'first_name' => 'First Name',
        'middle_name' => 'Middle Name',
        'last_name' => 'Last Name',
        'college' => 'College',
        'department' => 'Department',
        'phone' => 'Phone',
        'photo' => 'Photo',
        'laptop_type' => 'Laptop Type',
        'laptop_serial' => 'Serial',
        'qr_code' => 'QR Code',
        'action' => 'Action',
        'language' => 'Language',
        'rights' => 'All rights reserved.',
    ],
    'am' => [
        'dashboard' => 'የአስተዳዳሪ ዳሽቦርድ',
        'student' => 'ተማሪዎች',
        'logout' => 'ውጣ',
        'system_title' => 'የደብረ ብርሃን ዩኒቨርሲቲ የላፕቶፕ ውጪ ስርዓት',
        'add_new' => 'አዲስ ተማሪ መዝግብ',
        'search_placeholder' => 'ፈልግ...',
        'reset' => 'እንደገና ይጀምሩ',
        'export' => 'ወደ ኤክሴል ላክ',
        'delete_selected' => 'የተመረጡን አጥፋ',
        'id' => 'መለያ',
        'first_name' => 'ስም',
        'middle_name' => 'የአባት ስም',
        'last_name' => 'የአያት ስም',
        'college' => 'ኮሌጅ',
        'department' => 'ት/ት ክፍል',
        'phone' => 'ስልክ',
        'photo' => 'ፎቶ',
        'laptop_type' => 'የላፕቶፕ አይነት',
        'laptop_serial' => 'የላፕቶፕ ሴሪያል',
        'qr_code' => 'QR_ኮድ',
        'action' => 'እርምጃ',
        'language' => 'ቋንቋ',
        'rights' => 'መብቱ በህግ የተጠበቀ ነው።',
    ]
];

$t = $texts[$lang];

// ================= Delete Selected Records =================
if (isset($_POST['delete_selected']) && !empty($_POST['ids'])) {
    $ids = implode(",", array_map('intval', $_POST['ids']));
    $conn->query("DELETE FROM student WHERE id IN ($ids)");
    header("Location: Admin_student_registrations.php");
    exit;
}

// ================= Delete Single Record =================
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM student WHERE id=$id");
    header("Location: Admin_student_registrations.php");
    exit;
}

// ================= Export to Excel =================
if (isset($_POST['export_excel'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=students.xls");
    $result_excel = $conn->query("SELECT * FROM student");
    echo $t['id']."\t".$t['first_name']."\t".$t['middle_name']."\t".$t['last_name']."\t".$t['college']."\t".$t['department']."\t".$t['phone']."\t".$t['laptop_type']."\t".$t['laptop_serial']."\n";
    while ($row = $result_excel->fetch_assoc()) {
        echo $row['id']."\t".$row['fname']."\t".$row['mname']."\t".$row['lname']."\t".$row['college']."\t".$row['department']."\t".$row['phone']."\t".$row['laptop_type']."\t".$row['laptop_serial']."\n";
    }
    exit;
}

// ================= Pagination & Search =================
$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$search = "";
$where = "";
if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = $conn->real_escape_string($_GET['search']);
    $where = "WHERE fname LIKE '%$search%' OR mname LIKE '%$search%' OR lname LIKE '%$search%' OR college LIKE '%$search%' OR department LIKE '%$search%' OR phone LIKE '%$search%' OR laptop_serial LIKE '%$search%'";
}

$sql = "SELECT * FROM student $where LIMIT $start, $limit";
$result = $conn->query($sql);

$totalResult = $conn->query("SELECT COUNT(*) as total FROM student $where");
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['student'] ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="Student_registartions_style.css">
<link rel="stylesheet" href="Language_Style.css">

<script>
function toggleCheckboxes(master) {
    var checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => cb.checked = master.checked);
}
</script>
</head>
<body>

<div class="main-container">
    <div class="sidebar">
        <h2> DBULGP </h2>
        <a href="Admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> <?= $t['dashboard'] ?></a>
        <a href="Admin_student_registrations.php" class="active"><i class="fas fa-user-graduate"></i> <?= $t['student'] ?></a>
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
                    <a href="Admin_student_register.php" class="add-new"><?= $t['add_new'] ?></a>
                </div>

                <div class="top-right">
                    <form method="get" style="display:flex; gap:5px;">
                        <input type="text" name="search" placeholder="<?= $t['search_placeholder'] ?>" value="<?= htmlspecialchars($search) ?>">
                        <button type="submit"><?= $t['search_placeholder'] ?></button>
                        <a href="Admin_student_registrations.php" class="reset"><?= $t['reset'] ?></a>
                    </form>

                    <form method="post">
                        <button type="submit" name="export_excel"><?= $t['export'] ?></button>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <form method="post">
            <table>
                <tr>
                    <th><input type="checkbox" onclick="toggleCheckboxes(this)"></th>
                    <th><?= $t['id'] ?></th>
                    <th><?= $t['first_name'] ?></th>
                    <th><?= $t['middle_name'] ?></th>
                    <th><?= $t['last_name'] ?></th>
                    <th><?= $t['college'] ?></th>
                    <th><?= $t['department'] ?></th>
                    <th><?= $t['phone'] ?></th>
                    <th><?= $t['photo'] ?></th>
                    <th><?= $t['laptop_type'] ?></th>
                    <th><?= $t['laptop_serial'] ?></th>
                    <th><?= $t['qr_code'] ?></th>
                    <th><?= $t['action'] ?></th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>"></td>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fname']) ?></td>
                    <td><?= htmlspecialchars($row['mname']) ?></td>
                    <td><?= htmlspecialchars($row['lname']) ?></td>
                    <td><?= htmlspecialchars($row['college']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <?php if (!empty($row['photo']) && file_exists("uploads/".$row['photo'])): ?>
                            <img src="uploads/<?= $row['photo'] ?>" alt="photo" width="50">
                        <?php else: ?> No Photo <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['laptop_type']) ?></td>
                    <td><?= htmlspecialchars($row['laptop_serial']) ?></td>
                    <td><?= htmlspecialchars($row['qr_code']) ?></td>
                    <td>
                        <div class="action-btns">
                            <button class="action-dots"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="action-menu">
                                <a href="Admin_edit_student.php?id=<?= $row['id'] ?>">Edit</a>
                                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this student?')">Delete</a>
                                <a href="Admin_Generate_student_LGP_id.php?id=<?= $row['id'] ?>" target="_blank">Generate Student LGP ID</a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            </form>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Prev</a>
                <?php endif; ?>

                <?php for ($i=1; $i<=$totalPages; $i++): ?>
                    <?php if ($i >= $page-2 && $i <= $page+2): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= ($i==$page) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a>
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
