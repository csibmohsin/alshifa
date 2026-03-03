<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit; }

// --- HANDLE SAVE SETTINGS ---
if(isset($_POST['save_settings'])) {
    foreach($_POST['sets'] as $key => $val) {
        $val = $conn->real_escape_string($val);
        // Check if key exists
        $check = $conn->query("SELECT id FROM settings WHERE site_key='$key'");
        if($check->num_rows > 0) {
            $conn->query("UPDATE settings SET site_value='$val' WHERE site_key='$key'");
        } else {
            $conn->query("INSERT INTO settings (site_key, site_value) VALUES ('$key', '$val')");
        }
    }
    $msg = "Settings Updated Successfully!";
}

// Fetch current settings to fill the form
$current = [];
$res = $conn->query("SELECT * FROM settings");
while($row = $res->fetch_assoc()) { $current[$row['site_key']] = $row['site_value']; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Site Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger px-4 shadow-sm mb-4">
    <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-cogs"></i> General Settings
                </div>
                <div class="card-body">
                    <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="fw-bold">Lab Name</label>
                            <input type="text" name="sets[lab_name]" class="form-control" value="<?php echo $current['lab_name'] ?? ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Tagline (Hero Text)</label>
                            <input type="text" name="sets[tagline]" class="form-control" value="<?php echo $current['tagline'] ?? ''; ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Contact Phone</label>
                                <input type="text" name="sets[contact_phone]" class="form-control" value="<?php echo $current['contact_phone'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">WhatsApp Number</label>
                                <input type="text" name="sets[whatsapp]" class="form-control" value="<?php echo $current['whatsapp'] ?? ''; ?>">
                                <small class="text-muted">Format: 919876543210 (No + sign)</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Email Address</label>
                            <input type="text" name="sets[email]" class="form-control" value="<?php echo $current['email'] ?? ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Full Address</label>
                            <textarea name="sets[address]" class="form-control" rows="3"><?php echo $current['address'] ?? ''; ?></textarea>
                        </div>

                        <button type="submit" name="save_settings" class="btn btn-danger btn-lg w-100">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>