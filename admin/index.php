<?php
session_start();
include '../db.php'; 

// --- 1. AUTHENTICATION & LOGOUT ---
if(isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }

// --- 2. LOGIN LOGIC ---
if(isset($_POST['login'])) {
    $u = $_POST['username']; $p = $_POST['password'];
    $check = $conn->query("SELECT * FROM admin_users WHERE username='$u' AND password='$p'");
    if($check->num_rows > 0) { $_SESSION['admin'] = true; header("Location: index.php"); exit; } 
    else { $error = "Access Denied: Invalid Credentials"; }
}

// Show Login if not authenticated
if(!isset($_SESSION['admin'])) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login | Al Shifa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #800000 0%, #2c0b0e 100%); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Poppins', sans-serif; }
        .login-card { background: white; width: 400px; padding: 40px; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .form-control { background: #f8f9fa; border: 2px solid #eee; height: 50px; }
        .form-control:focus { border-color: #800000; box-shadow: none; }
        .btn-login { background: #800000; color: white; height: 50px; font-weight: 600; font-size: 16px; width: 100%; transition: 0.3s; }
        .btn-login:hover { background: #5a0000; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: #800000;">AL SHIFA</h2>
            <p class="text-muted">Secure Admin Access</p>
        </div>
        <form method="post">
            <div class="mb-3"><input name="username" class="form-control" placeholder="Username" required></div>
            <div class="mb-4"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
            <button name="login" class="btn btn-login rounded-pill">LOGIN DASHBOARD</button>
            <?php if(isset($error)) echo "<div class='alert alert-danger mt-3 text-center py-2'>$error</div>"; ?>
        </form>
    </div>
</body>
</html>
<?php exit; } 

// --- 3. GLOBAL LOGIC HANDLERS ---
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

// CRUD Operations
if(isset($_POST['add_test'])) {
    $conn->query("INSERT INTO tests (name, category, price, sample_type, fasting) VALUES ('{$_POST['name']}', '{$_POST['cat']}', '{$_POST['price']}', '{$_POST['sample']}', '{$_POST['fasting']}')");
    header("Location: index.php?tab=tests&msg=Test Added"); exit;
}
if(isset($_POST['add_pkg'])) {
    $conn->query("INSERT INTO packages (name, tests_included, original_price, offer_price, test_count, discount_percent) VALUES ('{$_POST['name']}', '{$_POST['desc']}', '{$_POST['oprice']}', '{$_POST['nprice']}', '{$_POST['tcount']}', '{$_POST['disc']}')");
    header("Location: index.php?tab=packages&msg=Package Created"); exit;
}
if(isset($_POST['update_offer'])) {
    $conn->query("TRUNCATE TABLE offers"); 
    $active = isset($_POST['active']) ? 1 : 0;
    $conn->query("INSERT INTO offers (title, description, is_active) VALUES ('{$_POST['title']}', '{$_POST['desc']}', $active)");
    header("Location: index.php?tab=offers&msg=Popup Updated"); exit;
}
if(isset($_POST['add_notice'])) { 
    $conn->query("INSERT INTO notices (message) VALUES ('{$_POST['msg']}')"); 
    header("Location: index.php?tab=notices&msg=Notice Posted"); exit;
}
if(isset($_POST['add_review'])) {
    $conn->query("INSERT INTO testimonials (name, location, message, rating) VALUES ('{$_POST['name']}', '{$_POST['loc']}', '{$_POST['msg']}', '{$_POST['rating']}')");
    header("Location: index.php?tab=reviews&msg=Review Added"); exit;
}

// Deletion Logic
if(isset($_GET['del'])) {
    $table = $_GET['tbl']; $id = $_GET['del']; $t = $_GET['tab'];
    $conn->query("DELETE FROM $table WHERE id=$id");
    header("Location: index.php?tab=$t&msg=Item Deleted"); exit;
}

// Stats
$cnt_test = $conn->query("SELECT COUNT(*) as c FROM tests")->fetch_assoc()['c'];
$cnt_pkg  = $conn->query("SELECT COUNT(*) as c FROM packages")->fetch_assoc()['c'];
$cnt_msg  = $conn->query("SELECT COUNT(*) as c FROM contact_messages")->fetch_assoc()['c'];
$cnt_doc  = $conn->query("SELECT COUNT(*) as c FROM staff")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #800000; --sidebar-bg: #2a0a0d; --active-bg: #4a1216; }
        body { font-family: 'Inter', sans-serif; background: #f4f6f9; }
        
        /* Sidebar */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--sidebar-bg); color: #ccc; transition: all 0.3s; z-index: 1000; }
        .sidebar-brand { padding: 25px 20px; font-size: 22px; font-weight: 700; color: white; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .nav-link { color: #cfd8dc; padding: 12px 20px; margin: 4px 10px; border-radius: 8px; font-weight: 500; transition: 0.3s; display: flex; align-items: center; }
        .nav-link:hover, .nav-link.active { background: var(--active-bg); color: white; transform: translateX(5px); }
        .nav-link i { width: 25px; margin-right: 10px; text-align: center; }
        
        /* Main Content */
        .main-content { margin-left: 260px; padding: 30px; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: white; transition: 0.3s; }
        .card-custom:hover { transform: translateY(-3px); }
        
        /* Stats Cards */
        .stat-card { display: flex; align-items: center; padding: 25px; border-radius: 12px; color: white; position: relative; overflow: hidden; }
        .stat-icon { font-size: 3rem; opacity: 0.2; position: absolute; right: 20px; bottom: 10px; }
        .bg-1 { background: linear-gradient(45deg, #FF512F, #DD2476); }
        .bg-2 { background: linear-gradient(45deg, #4776E6, #8E54E9); }
        .bg-3 { background: linear-gradient(45deg, #00b09b, #96c93d); }
        .bg-4 { background: linear-gradient(45deg, #F2994A, #F2C94C); }
        
        /* Tables & Forms */
        .table thead th { background: #f8f9fa; color: #555; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; border-bottom: 2px solid #eee; }
        .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
        
        /* Responsive */
        @media(max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-microscope text-warning me-2"></i> AL SHIFA ADMIN
        </div>
        <div class="py-3">
            <a href="?tab=dashboard" class="nav-link <?php if($tab=='dashboard') echo 'active'; ?>"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="?tab=inquiries" class="nav-link <?php if($tab=='inquiries') echo 'active'; ?>"><i class="fas fa-envelope"></i> Inquiries <span class="badge bg-danger ms-auto"><?php echo $cnt_msg; ?></span></a>
            <p class="text-uppercase small fw-bold px-4 mt-4 mb-2 text-muted">Management</p>
            <a href="?tab=tests" class="nav-link <?php if($tab=='tests') echo 'active'; ?>"><i class="fas fa-vial"></i> Lab Tests</a>
            <a href="?tab=packages" class="nav-link <?php if($tab=='packages') echo 'active'; ?>"><i class="fas fa-box-open"></i> Health Packages</a>
            <a href="?tab=reviews" class="nav-link <?php if($tab=='reviews') echo 'active'; ?>"><i class="fas fa-star"></i> Testimonials</a>
            <p class="text-uppercase small fw-bold px-4 mt-4 mb-2 text-muted">Site Controls</p>
            <a href="?tab=offers" class="nav-link <?php if($tab=='offers') echo 'active'; ?>"><i class="fas fa-bullhorn"></i> Popup Offer</a>
            <a href="?tab=notices" class="nav-link <?php if($tab=='notices') echo 'active'; ?>"><i class="fas fa-scroll"></i> Notice Board</a>
            <a href="staff.php" class="nav-link"><i class="fas fa-user-md"></i> Manage Staff</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cogs"></i> Settings</a>
            <a href="?logout=1" class="nav-link text-danger mt-3"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="main-content">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">
                    <?php echo ucfirst($tab); ?> Overview
                </h3>
                <p class="text-muted small m-0">Manage your lab efficiently.</p>
            </div>
            <a href="../index.php" target="_blank" class="btn btn-outline-dark rounded-pill px-4">
                <i class="fas fa-external-link-alt me-2"></i> Visit Website
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_GET['msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if($tab == 'dashboard'): ?>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card bg-1">
                    <div><h2 class="fw-bold m-0"><?php echo $cnt_msg; ?></h2><small>Pending Inquiries</small></div>
                    <i class="fas fa-envelope stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-2">
                    <div><h2 class="fw-bold m-0"><?php echo $cnt_test; ?></h2><small>Active Tests</small></div>
                    <i class="fas fa-flask stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-3">
                    <div><h2 class="fw-bold m-0"><?php echo $cnt_pkg; ?></h2><small>Packages</small></div>
                    <i class="fas fa-box stat-icon"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-4">
                    <div><h2 class="fw-bold m-0"><?php echo $cnt_doc; ?></h2><small>Doctors / Staff</small></div>
                    <i class="fas fa-user-md stat-icon"></i>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($tab == 'inquiries'): ?>
        <div class="card card-custom p-4">
            <table class="table table-hover align-middle">
                <thead><tr><th>Date</th><th>Patient</th><th>Phone</th><th>Message</th><th>Action</th></tr></thead>
                <tbody>
                    <?php $q=$conn->query("SELECT * FROM contact_messages ORDER BY id DESC"); 
                    if($q->num_rows > 0): while($r=$q->fetch_assoc()): ?>
                    <tr>
                        <td><small class="text-muted"><?php echo date('d M, h:i A', strtotime($r['created_at'])); ?></small></td>
                        <td class="fw-bold"><?php echo $r['name']; ?></td>
                        <td><a href="https://wa.me/91<?php echo $r['phone']; ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3"><i class="fab fa-whatsapp"></i> <?php echo $r['phone']; ?></a></td>
                        <td><?php echo $r['message']; ?></td>
                        <td><a href="?del=<?php echo $r['id']; ?>&tbl=contact_messages&tab=inquiries" class="btn btn-action btn-danger text-white" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td>
                    </tr>
                    <?php endwhile; else: echo "<tr><td colspan='5' class='text-center py-4 text-muted'>No new inquiries found.</td></tr>"; endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if($tab == 'tests'): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <h5 class="mb-3 fw-bold text-maroon">Add New Test</h5>
                    <form method="post">
                        <input name="name" class="form-control mb-2" placeholder="Test Name (e.g. CBC)" required>
                        <select name="cat" class="form-control mb-2">
                            <option value="">Select Category</option>
                            <option>Hematology</option><option>Biochemistry</option><option>Serology</option><option>Microbiology</option>
                        </select>
                        <div class="row g-2 mb-2">
                            <div class="col"><input name="price" class="form-control" placeholder="Price (₹)"></div>
                            <div class="col"><select name="fasting" class="form-control"><option value="No">No Fasting</option><option value="Yes">Fasting Req.</option></select></div>
                        </div>
                        <input name="sample" class="form-control mb-3" placeholder="Sample Type (Blood/Urine)">
                        <button name="add_test" class="btn btn-dark w-100">Add Test</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="fw-bold">Test Directory</h5>
                        <input type="text" id="search" class="form-control w-50 form-control-sm" placeholder="Search tests...">
                    </div>
                    <div style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-bordered table-sm" id="testTable">
                            <thead><tr><th>Name</th><th>Cat</th><th>Price</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php $q=$conn->query("SELECT * FROM tests ORDER BY id DESC"); while($r=$q->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $r['name']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $r['category']; ?></span></td>
                                    <td class="fw-bold">₹<?php echo $r['price']; ?></td>
                                    <td><a href="?del=<?php echo $r['id']; ?>&tbl=tests&tab=tests" class="text-danger"><i class="fas fa-trash-alt"></i></a></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($tab == 'packages'): ?>
        <div class="card card-custom p-4 mb-4">
            <h5 class="fw-bold text-maroon border-bottom pb-2 mb-3">Create New Package</h5>
            <form method="post">
                <div class="row g-3">
                    <div class="col-md-6"><label>Package Name</label><input name="name" class="form-control" required></div>
                    <div class="col-md-2"><label>Badge Count</label><input name="tcount" class="form-control" placeholder="127"></div>
                    <div class="col-md-2"><label>Discount %</label><input name="disc" class="form-control" placeholder="50"></div>
                    <div class="col-md-2"><label>Offer Price</label><input name="nprice" class="form-control" required></div>
                    <div class="col-md-12"><label>Original Price</label><input name="oprice" class="form-control"></div>
                    <div class="col-12"><label>Tests Included (Comma separated)</label><textarea name="desc" class="form-control" rows="2"></textarea></div>
                    <div class="col-12"><button name="add_pkg" class="btn btn-dark px-5">Create Package</button></div>
                </div>
            </form>
        </div>
        <div class="card card-custom p-4">
            <h5 class="fw-bold">Active Packages</h5>
            <table class="table align-middle">
                <thead><tr><th>Package Name</th><th>Badge</th><th>Price</th><th>Action</th></tr></thead>
                <tbody>
                    <?php $q=$conn->query("SELECT * FROM packages"); while($r=$q->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold"><?php echo $r['name']; ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo $r['test_count']; ?> Tests</span></td>
                        <td class="text-success fw-bold">₹<?php echo $r['offer_price']; ?></td>
                        <td><a href="?del=<?php echo $r['id']; ?>&tbl=packages&tab=packages" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if($tab == 'reviews'): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-maroon mb-3">Add Client Review</h5>
                    <form method="post">
                        <input name="name" class="form-control mb-2" placeholder="Client Name" required>
                        <input name="loc" class="form-control mb-2" placeholder="Location (e.g. Delhi)" required>
                        <select name="rating" class="form-control mb-2">
                            <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                        </select>
                        <textarea name="msg" class="form-control mb-3" rows="3" placeholder="Review message..." required></textarea>
                        <button name="add_review" class="btn btn-dark w-100">Save Review</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row g-3">
                    <?php $q=$conn->query("SELECT * FROM testimonials ORDER BY id DESC"); while($r=$q->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="card card-custom p-3 h-100 position-relative">
                            <a href="?del=<?php echo $r['id']; ?>&tbl=testimonials&tab=reviews" class="position-absolute top-0 end-0 m-2 text-danger"><i class="fas fa-times"></i></a>
                            <h6 class="fw-bold m-0"><?php echo $r['name']; ?></h6>
                            <small class="text-muted"><?php echo $r['location']; ?></small>
                            <div class="text-warning my-2"><?php echo str_repeat('★', $r['rating']); ?></div>
                            <p class="small text-muted mb-0">"<?php echo $r['message']; ?>"</p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if($tab == 'offers' || $tab == 'notices'): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-maroon"><i class="fas fa-gift me-2"></i> Homepage Popup</h5>
                        <?php $off = $conn->query("SELECT * FROM offers LIMIT 1")->fetch_assoc(); ?>
                        <span class="badge <?php echo ($off['is_active'])?'bg-success':'bg-secondary'; ?>">
                            <?php echo ($off['is_active'])?'ACTIVE':'DISABLED'; ?>
                        </span>
                    </div>
                    <form method="post">
                        <label class="small fw-bold">Title</label>
                        <input name="title" class="form-control mb-2" value="<?php echo $off['title'] ?? ''; ?>">
                        <label class="small fw-bold">Message</label>
                        <textarea name="desc" class="form-control mb-2" rows="3"><?php echo $off['description'] ?? ''; ?></textarea>
                        <div class="form-check form-switch mb-3 p-2 bg-light rounded">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="active" <?php echo ($off['is_active'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold">Enable on Website</label>
                        </div>
                        <button name="update_offer" class="btn btn-primary w-100">Update Popup</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom p-4 h-100">
                    <h5 class="fw-bold text-maroon mb-3"><i class="fas fa-scroll me-2"></i> News Ticker</h5>
                    <form method="post" class="input-group mb-3">
                        <input name="msg" class="form-control" placeholder="Type announcement..." required>
                        <button name="add_notice" class="btn btn-dark">Add</button>
                    </form>
                    <ul class="list-group list-group-flush">
                        <?php $n=$conn->query("SELECT * FROM notices"); while($r=$n->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-angle-right text-warning me-2"></i> <?php echo $r['message']; ?></span>
                            <a href="?del=<?php echo $r['id']; ?>&tbl=notices&tab=notices" class="text-danger small"><i class="fas fa-trash"></i></a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple Table Search Script
        document.getElementById('search')?.addEventListener('keyup', function() {
            let filter = this.value.toUpperCase();
            let rows = document.getElementById('testTable').getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                let txt = rows[i].textContent || rows[i].innerText;
                rows[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        });
    </script>
</body>
</html>