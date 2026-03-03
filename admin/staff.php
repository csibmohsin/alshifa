<?php
session_start();
include '../db.php';
// Check Login
if(!isset($_SESSION['admin'])) { header("Location: index.php"); exit; }

// --- HANDLE ADD STAFF ---
if(isset($_POST['add_staff'])) {
    $name = $_POST['name'];
    $desg = $_POST['desg'];
    
    // Image Upload Logic
    $target_dir = "../uploads/";
    if(!is_dir($target_dir)) mkdir($target_dir); // Create folder if not exists
    
    $filename = time() . "_" . basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . $filename;
    
    if(move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $conn->query("INSERT INTO staff (name, designation, image) VALUES ('$name', '$desg', '$filename')");
        $msg = "Staff Added Successfully!";
    } else {
        $error = "Failed to upload image.";
    }
}

// --- HANDLE DELETE STAFF ---
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Get image name to delete file
    $res = $conn->query("SELECT image FROM staff WHERE id=$id");
    $row = $res->fetch_assoc();
    if($row) { unlink("../uploads/" . $row['image']); } // Delete file from folder
    
    $conn->query("DELETE FROM staff WHERE id=$id");
    header("Location: staff.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger px-4 shadow-sm mb-4">
    <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">Add New Doctor/Staff</div>
                <div class="card-body">
                    <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>
                    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required placeholder="Dr. John Doe">
                        </div>
                        <div class="mb-3">
                            <label>Designation</label>
                            <input type="text" name="desg" class="form-control" required placeholder="MBBS, Pathologist">
                        </div>
                        <div class="mb-3">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control" required>
                            <small class="text-muted">JPG/PNG only</small>
                        </div>
                        <button type="submit" name="add_staff" class="btn btn-danger w-100">Add Staff</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white text-danger fw-bold">Current Staff List</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = $conn->query("SELECT * FROM staff ORDER BY id DESC");
                            while($row = $res->fetch_assoc()): 
                            ?>
                            <tr>
                                <td>
                                    <img src="../uploads/<?php echo $row['image']; ?>" width="50" height="50" class="rounded-circle object-fit-cover">
                                </td>
                                <td class="fw-bold"><?php echo $row['name']; ?></td>
                                <td><?php echo $row['designation']; ?></td>
                                <td>
                                    <a href="staff.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>