<?php
session_start();
$conn = new mysqli("localhost", "root", "", "room");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["addData"])) {
    $name = trim($_POST["name"]);
    $major = trim($_POST["major"]);

    if (!empty($name) && !empty($major)) {
        $sql = "INSERT INTO student (name, major) VALUES (?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $name, $major);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION["alert"] = ['success', 'สำเร็จ', 'เพิ่มข้อมูลนักศึกษา ' . htmlspecialchars($name) . ' เรียบร้อยแล้ว'];
    }
    header("Location: index.php");
    exit();
}

if (isset($_POST["updateId"])) {
    $userId = intval($_POST["id"]);
    $name = trim($_POST["name"]);
    $major = trim($_POST["major"]);

    if (!empty($name) && !empty($major)) {
        $sql = "UPDATE student SET name = ?, major = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssi", $name, $major, $userId);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION["alert"] = ['success', 'อัปเดตสำเร็จ', 'แก้ไขข้อมูลของ ' . htmlspecialchars($name) . ' เรียบร้อยแล้ว'];
    }
    header("Location: index.php");
    exit();
}

if (isset($_POST["deleteId"])) {
    $userId = intval($_POST["id"]);

    $sql = "DELETE FROM student WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION["alert"] = ['success', 'ลบข้อมูลสำเร็จ', 'ลบรายการรหัส #' . $userId . ' ออกจากระบบแล้ว'];
    header("Location: index.php");
    exit();
}
?>

<!doctype html>
<html lang="th" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ROOM Manager • Student System & CRUD Documentation</title>
    <!-- Bootstrap 5.3 CSS ONLY -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Black & Orange theme: white text -->
    <style>
        :root { color-scheme: dark; }
        html, body { background: #111111 !important; color: #ffffff !important; }
        body, .bg-body-secondary, .bg-body, .bg-body-tertiary,
        .card, .card-header, .accordion-item, .accordion-button,
        .navbar, .table, .table > :not(caption) > * > * {
            background-color: #171717 !important;
            color: #ffffff !important;
        }
        .card, .navbar, .accordion-item { border-color: #383838 !important; }
        .text-secondary, .text-body-secondary, .text-muted { color: #d0d0d0 !important; }
        .text-primary, .text-success, .text-info, .text-danger,
        .text-warning, .text-warning-emphasis, .text-info-emphasis,
        .text-success-emphasis { color: #ff8a00 !important; }
        .bg-primary, .badge.bg-primary, .btn-primary {
            background-color: #f58200 !important;
            border-color: #f58200 !important;
            color: #ffffff !important;
        }
        .btn-outline-primary, .btn-outline-secondary {
            color: #ffffff !important;
            border-color: #ff8a00 !important;
        }
        .btn-outline-primary:hover, .btn-outline-secondary:hover,
        .btn-primary:hover { background-color: #cc6d00 !important; color: #ffffff !important; }
        .border-primary, .border-success, .border-info, .border-danger,
        .border-warning { border-color: #ff8a00 !important; }
        .bg-primary.bg-opacity-10, .bg-success.bg-opacity-10,
        .bg-info.bg-opacity-10, .bg-warning.bg-opacity-10,
        .bg-danger.bg-opacity-10 { background-color: #292018 !important; }
        input.form-control, textarea.form-control, select.form-select {
            background-color: #242424 !important;
            color: #ffffff !important;
            border-color: #555555 !important;
        }
        input.form-control::placeholder { color: #cccccc !important; }
        .form-floating > label { color: #dddddd !important; }
        .table { --bs-table-color: #ffffff; --bs-table-bg: #171717; --bs-table-hover-color: #ffffff; --bs-table-hover-bg: #292929; }
        .table thead th { background-color: #222222 !important; color: #ffffff !important; }
        .accordion-button:not(.collapsed) { background-color: #302216 !important; color: #ff9b26 !important; }
        .accordion-button::after { filter: invert(1); }
        code { color: #ffad42 !important; }
        a { color: #ff9b26; }
        .btn-body { color: #ffffff !important; }
        .shadow-sm { --bs-box-shadow: 0 .125rem .25rem rgba(0,0,0,.35); }
    </style>

</head>

<body class="bg-body-secondary py-3" style="font-family: 'Kanit', sans-serif;">
    <?php
    if (isset($_SESSION['alert'])) {
        ?>
        <script>
            Swal.fire({
                icon: "<?php echo $_SESSION['alert'][0]; ?>",
                title: "<?php echo $_SESSION['alert'][1]; ?>",
                text: "<?php echo $_SESSION['alert'][2]; ?>",
                timer: 2500,
                showConfirmButton: false
            });
        </script>
        <?php
        unset($_SESSION['alert']);
    }
    ?>

    <!-- Top Header -->
    <header class="container mb-4">
        <nav class="navbar navbar-expand-lg bg-body rounded-4 shadow-sm px-4 py-3 border border-secondary border-opacity-10">
            <div class="container-fluid p-0 d-flex justify-content-between align-items-center">
                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center gap-3 m-0" href="index.php" style="font-family: 'Kanit', sans-serif;">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="bi bi-mortarboard-fill fs-4"></i>
                    </div>
                    <div>
                        <span class="fw-bold fs-4 d-block lh-1">ROOM<span class="text-primary">.io</span></span>
                        <span class="text-secondary small">ระบบจัดการข้อมูลนักศึกษา (CRUD) + คู่มือเรียนรู้</span>
                    </div>
                </a>

                <!-- Right Actions: Stats & Theme Toggle -->
                <div class="d-flex align-items-center gap-3">
                    <?php
                    $sql = "SELECT * FROM student ORDER BY id DESC";
                    $result = null;
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->execute();
                        $result = $stmt->get_result();
                    }
                    $totalRows = $result ? $result->num_rows : 0;
                    ?>
                    <div class="d-none d-md-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-body-tertiary border border-secondary border-opacity-10">
                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 p-0" style="width: 26px; height: 26px; font-size: 0.85rem;">
                            <?php echo $totalRows; ?>
                        </span>
                        <span class="small fw-medium text-secondary" style="font-family: 'Kanit', sans-serif;">นักศึกษาในระบบ</span>
                    </div>

                    <!-- Theme Switch Button (Fixed square 42x42 circle) -->
                    <button class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0" id="themeToggle" style="width: 42px; height: 42px;" title="เปลี่ยนโหมดมืด/สว่าง">
                        <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main class="container mb-5">
        <div class="row g-4 align-items-start">
            <!-- Left Column: Add Student Form -->
            <div class="col-lg-4 col-md-12">
                <!-- Explanation Badge for Create (C) -->
                <div class="badge bg-primary bg-opacity-10 text-primary mb-2 py-2 px-3 w-100 text-start rounded-3 border border-primary border-opacity-25" style="font-family: 'Kanit', sans-serif;">
                    <i class="bi bi-code-square me-1"></i> <strong>ส่วนที่ 1: Create (C)</strong> — เพิ่มข้อมูลด้วย Prepared Statement
                </div>

                <div class="card bg-body border-0 rounded-4 shadow-sm h-100 p-3">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-person-plus-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold m-0" style="font-family: 'Kanit', sans-serif;">เพิ่มข้อมูลนักศึกษา</h5>
                                <span class="text-secondary small" style="font-family: 'Kanit', sans-serif;">กรอกรายละเอียดเพื่อบันทึกใหม่</span>
                            </div>
                        </div>

                        <form action="" method="post" style="font-family: 'Kanit', sans-serif;">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-3 border-secondary border-opacity-25 bg-body-tertiary" id="floatingName" placeholder="ชื่อ-นามสกุล" name="name" style="font-family: 'Kanit', sans-serif;" required autocomplete="off">
                                <label for="floatingName" class="text-secondary"><i class="bi bi-person me-2"></i>ชื่อ-นามสกุล</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="text" class="form-control rounded-3 border-secondary border-opacity-25 bg-body-tertiary" id="floatingMajor" placeholder="สาขาวิชา" name="major" style="font-family: 'Kanit', sans-serif;" required autocomplete="off">
                                <label for="floatingMajor" class="text-secondary"><i class="bi bi-journal-text me-2"></i>สาขาวิชา</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" name="addData" style="font-family: 'Kanit', sans-serif;">
                                <i class="bi bi-plus-lg fs-5"></i> บันทึกนักศึกษาใหม่
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Tech Notes -->
                <div class="card border-0 bg-info bg-opacity-10 rounded-4 mt-3 p-3">
                    <div class="d-flex align-items-center gap-3 text-info-emphasis">
                        <i class="bi bi-lightbulb-fill fs-4 flex-shrink-0"></i>
                        <span class="small fw-medium" style="font-family: 'Kanit', sans-serif;">
                            <strong>ทิปส์การใช้งาน:</strong> คลิกแก้ไขชื่อหรือสาขาวิชาในตารางด้านขวา แล้วกดปุ่ม <code>อัปเดต</code> ได้เลยทันที (Inline Editing)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Student Data Table -->
            <div class="col-lg-8 col-md-12">
                <!-- Explanation Badge for Read / Update / Delete (R-U-D) -->
                <div class="badge bg-success bg-opacity-10 text-success mb-2 py-2 px-3 w-100 text-start rounded-3 border border-success border-opacity-25" style="font-family: 'Kanit', sans-serif;">
                    <i class="bi bi-code-square me-1"></i> <strong>ส่วนที่ 2: Read (R) • Update (U) • Delete (D)</strong> — ตารางอัจฉริยะที่แก้ไขและลบข้อมูลได้ทันที
                </div>

                <div class="card bg-body border-0 rounded-4 shadow-sm overflow-hidden">
                    <!-- Card Header -->
                    <div class="card-header bg-transparent border-bottom border-secondary border-opacity-10 p-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold m-0" style="font-family: 'Kanit', sans-serif;">รายชื่อนักศึกษาทั้งหมด</h5>
                                <span class="text-secondary small" style="font-family: 'Kanit', sans-serif;">จัดการ แก้ไข และลบข้อมูลนักศึกษา</span>
                            </div>
                        </div>
                        <span class="badge bg-body-tertiary text-secondary border border-secondary border-opacity-25 rounded-pill py-2 px-3 fw-normal" style="font-family: 'Kanit', sans-serif;">
                            <i class="bi bi-database me-1"></i> อัปเดตล่าสุด
                        </span>
                    </div>

                    <!-- Table Body -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-family: 'Kanit', sans-serif;">
                                <thead class="bg-body-tertiary text-secondary border-bottom border-secondary border-opacity-10">
                                    <tr>
                                        <th scope="col" class="py-3 ps-4 text-center" style="width: 60px;">#</th>
                                        <th scope="col" class="py-3">ชื่อ-นามสกุล</th>
                                        <th scope="col" class="py-3">สาขาวิชา</th>
                                        <th scope="col" class="py-3" style="width: 160px;">เวลาบันทึก</th>
                                        <th scope="col" class="py-3 pe-4 text-end" style="width: 180px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($totalRows > 0) {
                                        $avatarColors = [
                                            'bg-primary bg-opacity-10 text-primary',
                                            'bg-success bg-opacity-10 text-success',
                                            'bg-warning bg-opacity-10 text-warning-emphasis',
                                            'bg-info bg-opacity-10 text-info-emphasis',
                                            'bg-danger bg-opacity-10 text-danger'
                                        ];
                                        $colorIndex = 0;

                                        while ($row = $result->fetch_assoc()) {
                                            $rowId = $row['id'];
                                            $firstChar = mb_substr(trim($row['name']), 0, 1, 'UTF-8');
                                            if (empty($firstChar)) $firstChar = '?';
                                            $badgeColor = $avatarColors[$colorIndex % count($avatarColors)];
                                            $colorIndex++;
                                            ?>
                                            <tr>
                                                <!-- Row Form -->
                                                <th scope="row" class="ps-4 text-center fw-medium text-secondary">
                                                    <?php echo $rowId; ?>
                                                    <form id="form-<?php echo $rowId; ?>" action="" method="post" class="d-none">
                                                        <input type="hidden" value="<?php echo $rowId; ?>" name="id">
                                                    </form>
                                                </th>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="badge rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 p-0 <?php echo $badgeColor; ?>" style="width: 40px; height: 40px; font-size: 1rem;">
                                                            <?php echo htmlspecialchars($firstChar); ?>
                                                        </span>
                                                        <input type="text" class="form-control form-control-sm rounded-3 border-0 bg-body-tertiary py-2 px-3 fw-medium shadow-none" value="<?php echo htmlspecialchars($row['name']); ?>" name="name" form="form-<?php echo $rowId; ?>" style="font-family: 'Kanit', sans-serif;" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm rounded-3 border-0 bg-body-tertiary py-2 px-3 fw-medium shadow-none" value="<?php echo htmlspecialchars($row['major']); ?>" name="major" form="form-<?php echo $rowId; ?>" style="font-family: 'Kanit', sans-serif;" required>
                                                </td>
                                                <td>
                                                    <span class="badge bg-body-tertiary text-secondary border border-secondary border-opacity-10 rounded-pill py-1.5 px-2.5 fw-normal d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-clock-history"></i> <?php echo date('H:i, d/m/y', strtotime($row['timestamp'])); ?>
                                                    </span>
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <div class="btn-group btn-group-sm shadow-sm rounded-pill overflow-hidden border border-secondary border-opacity-25" role="group">
                                                        <button type="submit" class="btn btn-body bg-body text-primary px-3 fw-medium border-0" name="updateId" form="form-<?php echo $rowId; ?>" style="font-family: 'Kanit', sans-serif;" title="บันทึกการแก้ไข">
                                                            <i class="bi bi-check2-circle me-1"></i>อัปเดต
                                                        </button>
                                                        <button type="submit" class="btn btn-body bg-body text-danger px-2 border-start border-secondary border-opacity-25" name="deleteId" form="form-<?php echo $rowId; ?>" title="ลบข้อมูล" onclick="return confirm('ยืนยันลบข้อมูลนักศึกษา #<?php echo $rowId; ?> ออกจากระบบหรือไม่?');">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="py-4">
                                                    <div class="badge bg-body-tertiary text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-0" style="width: 64px; height: 64px;">
                                                        <i class="bi bi-inbox fs-1"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-1" style="font-family: 'Kanit', sans-serif;">ยังไม่มีข้อมูลนักศึกษาในระบบ</h6>
                                                    <span class="text-secondary small d-block" style="font-family: 'Kanit', sans-serif;">เริ่มต้นด้วยการกรอกชื่อและสาขาวิชาในฟอร์มด้านซ้ายมือ</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Educational Accordion Embedded in Web Page -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-body border-0 rounded-4 shadow-sm p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                            <i class="bi bi-book-half fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold m-0" style="font-family: 'Kanit', sans-serif;">📖 คู่มืออธิบายการทำงานของโค้ด & ระบบ CRUD (Embedded Documentation)</h5>
                            <span class="text-secondary small" style="font-family: 'Kanit', sans-serif;">คลิกที่แต่ละหัวข้อด้านล่างเพื่อทำความเข้าใจกลไกการทำงานของ PHP, MySQL และ Bootstrap 5.3</span>
                        </div>
                    </div>

                    <div class="accordion" id="crudDocumentationAccordion" style="font-family: 'Kanit', sans-serif;">
                        <!-- Accordion 1: Create -->
                        <div class="accordion-item border border-secondary border-opacity-10 rounded-3 mb-2 overflow-hidden bg-body-tertiary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-primary bg-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCreate" style="font-family: 'Kanit', sans-serif;">
                                    <i class="bi bi-plus-circle-fill me-2"></i> 1. Create (C) — การเพิ่มข้อมูลนักศึกษาเข้าสู่ฐานข้อมูล
                                </button>
                            </h2>
                            <div id="collapseCreate" class="accordion-collapse collapse" data-bs-parent="#crudDocumentationAccordion">
                                <div class="accordion-body text-secondary small lh-lg">
                                    <p class="mb-2"><strong>🛠️ การทำงานของโค้ด PHP (บรรทัดที่ 9-24):</strong></p>
                                    <ul>
                                        <li>เมื่อผู้ใช้กรอกชื่อและสาขาแล้วกดปุ่ม <code>บันทึกนักศึกษาใหม่</code> ฟอร์มจะส่งค่าแบบ <code>POST</code> โดยมี key ชื่อ <code>addData</code></li>
                                        <li>ใช้ฟังก์ชัน <code>trim($_POST['name'])</code> เพื่อตัดช่องว่างหัว-ท้าย และตรวจสอบไม่ให้เป็นค่าว่าง</li>
                                        <li>ใช้คำสั่ง SQL: <code>INSERT INTO student (name, major) VALUES (?, ?)</code> คู่กับ <strong>Prepared Statement</strong> (<code>$stmt->bind_param("ss", $name, $major)</code>) เพื่อป้องกันการโจมตีแบบ SQL Injection 100%</li>
                                    </ul>
                                    <p class="mb-0"><strong>🎨 ส่วนของ UI (Bootstrap 5.3):</strong> ใช้คลาส <code>form-floating</code> ทำให้ป้ายชื่อ (Label) ลอยขึ้นอัตโนมัติเมื่อกดพิมพ์ข้อความ ดูทันสมัยและประหยัดพื้นที่</p>
                                </div>
                            </div>
                        </div>

                        <!-- Accordion 2: Read -->
                        <div class="accordion-item border border-secondary border-opacity-10 rounded-3 mb-2 overflow-hidden bg-body-tertiary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-success bg-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRead" style="font-family: 'Kanit', sans-serif;">
                                    <i class="bi bi-list-check me-2"></i> 2. Read (R) — การดึงข้อมูลมาแสดงผลในตารางพร้อม Avatar วงกลม
                                </button>
                            </h2>
                            <div id="collapseRead" class="accordion-collapse collapse" data-bs-parent="#crudDocumentationAccordion">
                                <div class="accordion-body text-secondary small lh-lg">
                                    <p class="mb-2"><strong>🛠️ การทำงานของโค้ด PHP (บรรทัดที่ 111-117 และ 164-245):</strong></p>
                                    <ul>
                                        <li>ใช้คำสั่ง SQL: <code>SELECT * FROM student ORDER BY id DESC</code> ดึงข้อมูลนักศึกษาทั้งหมดโดยเรียงจากรายการล่าสุดอยู่ด้านบน</li>
                                        <li>นับจำนวนรายการด้วย <code>$result->num_rows</code> และวนลูป <code>while ($row = $result->fetch_assoc())</code> เพื่อสร้างแถวในตาราง HTML</li>
                                        <li>สร้างฟีเจอร์ <strong>Avatar Badge</strong> ดึงตัวอักษรแรกของชื่อ (<code>mb_substr</code>) มาใส่ในวงกลมสลับสีพาสเทลอัตโนมัติ (Primary, Success, Warning, Info, Danger)</li>
                                    </ul>
                                    <p class="mb-0"><strong>🎨 ส่วนของ UI (Bootstrap 5.3):</strong> ใช้ <code>table-responsive</code> ทำให้ตารางเลื่อนแนวนอนได้ในหน้าจอมือถือโดยไม่เสียทรง และมี <strong>Empty State</strong> แสดงกล่องข้อความเตือนเมื่อยังไม่มีข้อมูล</p>
                                </div>
                            </div>
                        </div>

                        <!-- Accordion 3: Update -->
                        <div class="accordion-item border border-secondary border-opacity-10 rounded-3 mb-2 overflow-hidden bg-body-tertiary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-info bg-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUpdate" style="font-family: 'Kanit', sans-serif;">
                                    <i class="bi bi-pencil-square me-2"></i> 3. Update (U) — การแก้ไขข้อมูลโดยตรงในตาราง (Inline Editing)
                                </button>
                            </h2>
                            <div id="collapseUpdate" class="accordion-collapse collapse" data-bs-parent="#crudDocumentationAccordion">
                                <div class="accordion-body text-secondary small lh-lg">
                                    <p class="mb-2"><strong>🛠️ การทำงานของโค้ด PHP & HTML5 (บรรทัดที่ 26-42 และ 182-200):</strong></p>
                                    <ul>
                                        <li><strong>เทคนิค HTML5 Form Attribute:</strong> ป้องกันปัญหาการใส่ <code>&lt;form&gt;</code> ใน <code>&lt;tr&gt;</code> (ซึ่งผิดหลัก W3C) โดยการสร้าง <code>&lt;form id="form-$rowId" class="d-none"&gt;</code> ซ่อนไว้ และใส่ attribute <code>form="form-$rowId"</code> ให้กับ input และปุ่มในแถวนั้นๆ แทน</li>
                                        <li>เมื่อผู้ใช้คลิกพิมพ์ชื่อหรือสาขาใหม่ในตาราง แล้วกดปุ่ม <code>อัปเดต</code> (`name="updateId"`) ระบบจะส่งคำสั่ง SQL: <code>UPDATE student SET name = ?, major = ? WHERE id = ?</code> ไปอัปเดตข้อมูลทันที</li>
                                    </ul>
                                    <p class="mb-0"><strong>🎨 ส่วนของ UI (Bootstrap 5.3):</strong> ช่อง input ในตารางถูกจัดสไตล์ด้วย <code>form-control-sm border-0 bg-body-tertiary shadow-none</code> ทำให้ดูเหมือนข้อความธรรมดาแต่คลิกแก้ไขได้ทันที</p>
                                </div>
                            </div>
                        </div>

                        <!-- Accordion 4: Delete -->
                        <div class="accordion-item border border-secondary border-opacity-10 rounded-3 mb-2 overflow-hidden bg-body-tertiary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-danger bg-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDelete" style="font-family: 'Kanit', sans-serif;">
                                    <i class="bi bi-trash3-fill me-2"></i> 4. Delete (D) — การลบข้อมูลพร้อมหน้าต่างยืนยันความปลอดภัย
                                </button>
                            </h2>
                            <div id="collapseDelete" class="accordion-collapse collapse" data-bs-parent="#crudDocumentationAccordion">
                                <div class="accordion-body text-secondary small lh-lg">
                                    <p class="mb-2"><strong>🛠️ การทำงานของโค้ด PHP & JS (บรรทัดที่ 44-56 และ 211):</strong></p>
                                    <ul>
                                        <li>เมื่อคลิกปุ่มลบ (`name="deleteId"`) ปุ่มจะทำงานร่วมกับ JavaScript <code>onclick="return confirm('ยืนยันลบข้อมูล...?');"</code> เพื่อถามความแน่ใจก่อนทุกครั้ง ป้องกันการเผลอกดลบ</li>
                                        <li>เมื่อยืนยันแล้ว ระบบจะใช้คำสั่ง SQL: <code>DELETE FROM student WHERE id = ?</code> ลบข้อมูลออกจากฐานข้อมูล</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Accordion 5: PRG Pattern & Theme -->
                        <div class="accordion-item border border-secondary border-opacity-10 rounded-3 mb-0 overflow-hidden bg-body-tertiary">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-warning-emphasis bg-body" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTech" style="font-family: 'Kanit', sans-serif;">
                                    <i class="bi bi-shield-check me-2"></i> 5. เทคนิคพิเศษ: PRG Pattern, SweetAlert2 และระบบ Dark Mode
                                </button>
                            </h2>
                            <div id="collapseTech" class="accordion-collapse collapse" data-bs-parent="#crudDocumentationAccordion">
                                <div class="accordion-body text-secondary small lh-lg">
                                    <ul>
                                        <li><strong>Post/Redirect/Get (PRG Pattern):</strong> ทุกครั้งที่มีการเพิ่ม แก้ไข หรือลบข้อมูล จะมีคำสั่ง <code>header("Location: index.php"); exit();</code> ในตอนท้าย เพื่อไม่ให้เบราว์เซอร์แจ้งเตือน Form Resubmission เมื่อผู้ใช้กด Refresh (F5)</li>
                                        <li><strong>SweetAlert2 Notifications:</strong> ใช้ Session <code>$_SESSION['alert']</code> เก็บข้อความแจ้งเตือน และเรียกใช้ `Swal.fire()` พร้อมตั้งเวลาปิดอัตโนมัติ 2.5 วินาที (<code>timer: 2500</code>)</li>
                                        <li><strong>Pure Bootstrap 5.3 Dark Mode:</strong> ใช้ attribute <code>data-bs-theme="light/dark"</code> ควบคุมผ่าน JavaScript สลับสีทั้งระบบทันทีโดยไม่เขียน CSS เพิ่ม พร้อมบันทึกสถานะไว้ใน <code>localStorage</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const htmlElement = document.documentElement;
            const themeToggleBtn = document.getElementById('themeToggle');
            const themeIcon = document.getElementById('themeIcon');

            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                setTheme(newTheme);
            });

            function setTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill fs-5 text-warning';
                    themeToggleBtn.title = 'เปลี่ยนเป็นโหมดสว่าง';
                } else {
                    themeIcon.className = 'bi bi-moon-stars-fill fs-5 text-secondary';
                    themeToggleBtn.title = 'เปลี่ยนเป็นโหมดมืด';
                }
            }
        });
    </script>
</body>

</html>