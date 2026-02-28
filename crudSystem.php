<?php
$host   = "localhost";
$user   = "root";
$pass   = "";
$dbname = "school_db";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);
$conn->query("CREATE TABLE IF NOT EXISTS `students` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `father_name` VARCHAR(100) NOT NULL,
    `email`       VARCHAR(100) NOT NULL,
    `phone`       VARCHAR(20)  NOT NULL,
    `age`         INT          NOT NULL,
    `gender`      ENUM('Male','Female','Other') NOT NULL,
    `city`        VARCHAR(80)  NOT NULL,
    `course`      VARCHAR(100) NOT NULL,
    `marks`       DECIMAL(5,2) NOT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$toast   = "";
$toast_type = "";
$edit_data  = null;

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("DELETE FROM students WHERE id=$id")) {
        $toast = "Student record deleted successfully.";
        $toast_type = "delete";
    } else {
        $toast = "Error deleting record: " . $conn->error;
        $toast_type = "error";
    }
}

// INSERT
if (isset($_POST['action']) && $_POST['action'] === 'insert') {
    $name   = $conn->real_escape_string(trim($_POST['name']));
    $fname  = $conn->real_escape_string(trim($_POST['father_name']));
    $email  = $conn->real_escape_string(trim($_POST['email']));
    $phone  = $conn->real_escape_string(trim($_POST['phone']));
    $age    = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $city   = $conn->real_escape_string(trim($_POST['city']));
    $course = $conn->real_escape_string(trim($_POST['course']));
    $marks  = (float)$_POST['marks'];
    $sql = "INSERT INTO students (name,father_name,email,phone,age,gender,city,course,marks)
            VALUES ('$name','$fname','$email','$phone',$age,'$gender','$city','$course',$marks)";
    if ($conn->query($sql)) {
        $toast = "Student added successfully.";
        $toast_type = "success";
    } else {
        $toast = "Error adding record: " . $conn->error;
        $toast_type = "error";
    }
}

// UPDATE
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id     = (int)$_POST['id'];
    $name   = $conn->real_escape_string(trim($_POST['name']));
    $fname  = $conn->real_escape_string(trim($_POST['father_name']));
    $email  = $conn->real_escape_string(trim($_POST['email']));
    $phone  = $conn->real_escape_string(trim($_POST['phone']));
    $age    = (int)$_POST['age'];
    $gender = $conn->real_escape_string($_POST['gender']);
    $city   = $conn->real_escape_string(trim($_POST['city']));
    $course = $conn->real_escape_string(trim($_POST['course']));
    $marks  = (float)$_POST['marks'];
    $sql = "UPDATE students SET name='$name',father_name='$fname',email='$email',
            phone='$phone',age=$age,gender='$gender',city='$city',course='$course',marks=$marks
            WHERE id=$id";
    if ($conn->query($sql)) {
        $toast = "Student record updated successfully.";
        $toast_type = "update";
    } else {
        $toast = "Error updating record: " . $conn->error;
        $toast_type = "error";
    }
}

// FETCH FOR EDIT
if (isset($_GET['edit'])) {
    $id        = (int)$_GET['edit'];
    $result    = $conn->query("SELECT * FROM students WHERE id=$id");
    $edit_data = $result->fetch_assoc();
}

$records = $conn->query("SELECT * FROM students ORDER BY id DESC");
$total   = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Records</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, sans-serif;
    background: #f0f2f5;
    color: #333;
    padding: 30px 20px;
}

h1 {
    font-size: 1.6rem;
    margin-bottom: 4px;
    color: #1a1a2e;
}

.subtitle {
    color: #888;
    font-size: 0.88rem;
    margin-bottom: 24px;
}

/* CARD */
.card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    padding: 24px;
    margin-bottom: 24px;
    max-width: 960px;
    margin-left: auto;
    margin-right: auto;
}

.card h2 {
    font-size: 1rem;
    color: #1a1a2e;
    margin-bottom: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f2f5;
}

/* FORM */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 14px;
}

.form-group { display: flex; flex-direction: column; gap: 5px; }

.form-group label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.form-group input,
.form-group select {
    padding: 9px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #333;
    background: #fafafa;
    outline: none;
    transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #4a6cf7;
    background: #fff;
}

.btn-row { display: flex; gap: 10px; margin-top: 18px; }

.btn {
    padding: 9px 22px;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.15s;
}
.btn:hover { opacity: 0.88; transform: translateY(-1px); }

.btn-primary { background: #4a6cf7; color: #fff; }
.btn-reset   { background: #e9ecef; color: #555; }

/* STATS */
.stat-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
    max-width: 960px;
    margin-left: auto;
    margin-right: auto;
}
.stat-box {
    background: #fff;
    border-radius: 8px;
    padding: 12px 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    font-size: 0.82rem;
    color: #888;
}
.stat-box strong { font-size: 1.3rem; color: #4a6cf7; display: block; }

/* TABLE */
.table-wrap { overflow-x: auto; }

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.88rem;
}

thead th {
    background: #f7f8fc;
    padding: 11px 14px;
    text-align: left;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #888;
    border-bottom: 2px solid #eee;
    white-space: nowrap;
}

tbody tr { border-bottom: 1px solid #f0f0f0; transition: background 0.15s; }
tbody tr:hover { background: #f7f8fc; }
tbody td { padding: 12px 14px; vertical-align: middle; }

.id-badge {
    display: inline-block;
    background: #eef1ff;
    color: #4a6cf7;
    font-weight: 700;
    font-size: 0.75rem;
    padding: 3px 10px;
    border-radius: 5px;
    border: 1px solid #d0d8ff;
}

.marks-high { color: #22a06b; font-weight: 700; }
.marks-mid  { color: #e07b00; font-weight: 700; }
.marks-low  { color: #d93025; font-weight: 700; }

.gender-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.gender-male   { background: #eef1ff; color: #4a6cf7; }
.gender-female { background: #fce8f0; color: #c0397a; }
.gender-other  { background: #fff4e5; color: #b76d00; }

.action-group { display: flex; gap: 6px; }

.btn-edit, .btn-del {
    padding: 5px 14px;
    border: none;
    border-radius: 5px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}
.btn-edit { background: #eef1ff; color: #4a6cf7; }
.btn-del  { background: #fde8e8; color: #d93025; }
.btn-edit:hover { background: #d0d8ff; }
.btn-del:hover  { background: #f5c0c0; }

.no-data {
    text-align: center;
    color: #aaa;
    padding: 40px;
    font-size: 0.9rem;
}

/* TOAST */
.toast-wrap {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    border-radius: 10px;
    min-width: 260px;
    max-width: 320px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    animation: slideIn 0.35s ease forwards;
    position: relative;
    overflow: hidden;
}

@keyframes slideIn {
    from { transform: translateX(110%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}

.toast-success { background: #f0fdf6; border: 1px solid #bbf0d6; }
.toast-update  { background: #eff6ff; border: 1px solid #bfdbfe; }
.toast-delete  { background: #fff8ed; border: 1px solid #fde68a; }
.toast-error   { background: #fef2f2; border: 1px solid #fecaca; }

.toast-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 1rem;
}
.toast-success .toast-icon { background: #d1fae5; }
.toast-update  .toast-icon { background: #dbeafe; }
.toast-delete  .toast-icon { background: #fef3c7; }
.toast-error   .toast-icon { background: #fee2e2; }

.toast-dot {
    width: 10px; height: 10px; border-radius: 50%; margin-top: 3px; flex-shrink: 0;
}
.toast-success .toast-dot { background: #22a06b; }
.toast-update  .toast-dot { background: #4a6cf7; }
.toast-delete  .toast-dot { background: #e07b00; }
.toast-error   .toast-dot { background: #d93025; }

.toast-body { flex: 1; }

.toast-title {
    font-size: 0.82rem;
    font-weight: 700;
    margin-bottom: 2px;
}
.toast-success .toast-title { color: #166534; }
.toast-update  .toast-title { color: #1e40af; }
.toast-delete  .toast-title { color: #92400e; }
.toast-error   .toast-title { color: #991b1b; }

.toast-msg {
    font-size: 0.78rem;
    color: #666;
    line-height: 1.4;
}

.toast-bar {
    position: absolute;
    bottom: 0; left: 0;
    height: 3px;
    animation: shrink 3.5s linear forwards;
    border-radius: 0 0 10px 10px;
}
.toast-success .toast-bar { background: #22a06b; }
.toast-update  .toast-bar { background: #4a6cf7; }
.toast-delete  .toast-bar { background: #e07b00; }
.toast-error   .toast-bar { background: #d93025; }

@keyframes shrink {
    from { width: 100%; }
    to   { width: 0%; }
}
</style>
</head>
<body>

<?php if ($toast): ?>
<div class="toast-wrap" id="toast-wrap">
    <?php
    $titles = [
        'success' => 'Student Added',
        'update'  => 'Record Updated',
        'delete'  => 'Record Deleted',
        'error'   => 'Something went wrong',
    ];
    ?>
    <div class="toast toast-<?= $toast_type ?>">
        <div class="toast-dot"></div>
        <div class="toast-body">
            <div class="toast-title"><?= $titles[$toast_type] ?? 'Notice' ?></div>
            <div class="toast-msg"><?= htmlspecialchars($toast) ?></div>
        </div>
        <div class="toast-bar"></div>
    </div>
</div>
<script>
    setTimeout(function() {
        var w = document.getElementById('toast-wrap');
        if (w) w.style.display = 'none';
    }, 3500);
</script>
<?php endif; ?>

<div style="max-width:960px; margin:0 auto;">

    <h1>Student Records</h1>
    <p class="subtitle">Manage student information — add, edit, view and delete records.</p>

    <!-- FORM -->
    <div class="card">
        <h2><?= $edit_data ? 'Edit Student Record' : 'Add New Student' ?></h2>
        <form method="POST" action="crudSystem.php">
            <input type="hidden" name="action" value="<?= $edit_data ? 'update' : 'insert' ?>">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <?php endif; ?>
            <div class="form-grid">
                <div class="form-group">
                    <label>Student Name</label>
                    <input type="text" name="name" placeholder="e.g. Ali Hassan" required
                           value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Father's Name</label>
                    <input type="text" name="father_name" placeholder="e.g. Muhammad Raza" required
                           value="<?= htmlspecialchars($edit_data['father_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="student@email.com" required
                           value="<?= htmlspecialchars($edit_data['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="03XX-XXXXXXX" required
                           value="<?= htmlspecialchars($edit_data['phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" placeholder="e.g. 20" min="5" max="60" required
                           value="<?= htmlspecialchars($edit_data['age'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">-- Select --</option>
                        <option value="Male"   <?= ($edit_data['gender'] ?? '') === 'Male'   ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($edit_data['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other"  <?= ($edit_data['gender'] ?? '') === 'Other'  ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" placeholder="e.g. Lahore" required
                           value="<?= htmlspecialchars($edit_data['city'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <input type="text" name="course" placeholder="e.g. Computer Science" required
                           value="<?= htmlspecialchars($edit_data['course'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Marks (out of 100)</label>
                    <input type="number" name="marks" placeholder="e.g. 85" step="0.01" min="0" max="100" required
                           value="<?= htmlspecialchars($edit_data['marks'] ?? '') ?>">
                </div>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn-primary">
                    <?= $edit_data ? 'Update Record' : 'Add Student' ?>
                </button>
                <a href="crudSystem.php" class="btn btn-reset" style="text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>

    <!-- STATS -->
    <div class="stat-bar">
        <div class="stat-box">
            <strong><?= $total ?></strong>
            Total Students
        </div>
        <?php
        $pass = $conn->query("SELECT COUNT(*) as c FROM students WHERE marks >= 50")->fetch_assoc()['c'];
        $avg  = $conn->query("SELECT AVG(marks) as a FROM students")->fetch_assoc()['a'];
        ?>
        <div class="stat-box">
            <strong><?= $pass ?></strong>
            Passed
        </div>
        <div class="stat-box">
            <strong><?= $avg ? number_format($avg, 1) : '0' ?>%</strong>
            Average Marks
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <h2>All Student Records</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Father's Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($records && $records->num_rows > 0): ?>
                    <?php while ($row = $records->fetch_assoc()): ?>
                        <?php
                            $m = (float)$row['marks'];
                            $mc = $m >= 80 ? 'marks-high' : ($m >= 50 ? 'marks-mid' : 'marks-low');
                            $gc = 'gender-' . strtolower($row['gender']);
                        ?>
                        <tr>
                            <td><span class="id-badge">#<?= str_pad($row['id'], 3, '0', STR_PAD_LEFT) ?></span></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['father_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td class="<?= $mc ?>"><?= $row['marks'] ?>%</td>
                            <td>
                                <div class="action-group">
                                    <a href="crudSystem.php?edit=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="crudSystem.php?delete=<?= $row['id'] ?>"
                                       class="btn-del"
                                       onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-data">No records found. Add a student to get started.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
<?php $conn->close(); ?>