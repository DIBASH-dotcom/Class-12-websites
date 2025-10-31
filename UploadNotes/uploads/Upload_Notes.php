<?php
require '../database.php'; // Adjust the path if needed

// Handle upload
$message = "";
if (isset($_POST['upload'])) {
    // Collect form data
    $name        = $_POST['name'];
    $faculty     = $_POST['faculty'];
    $subject     = $_POST['subject'];
    $course_type = $_POST['course_type'];
    $province    = $_POST['province'];
    $year        = $_POST['year'];
    $description = $_POST['description'];

    // File data
    $file_name = $_FILES['note_file']['name'];
    $file_tmp  = $_FILES['note_file']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_path = "uploads/" . basename($file_name);

    // Allowed extensions
    $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];
    if (!in_array($file_ext, $allowed_extensions)) {
        $message = "❌ Only PDF, DOC, DOCX, PPT, PPTX or TXT files are allowed.";
    } else {
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert into DB
            $sql = "INSERT INTO notes (name, faculty, subject, course_type, province, year, file_path, description)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $con->prepare($sql);
            $stmt->bind_param("ssssssss", $name, $faculty, $subject, $course_type, $province, $year, $file_path, $description);

            if ($stmt->execute()) {
                $message = "✅ Note uploaded and saved successfully!";
            } else {
                $message = "❌ Database error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "❌ Failed to upload the file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Notes</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            padding: 40px;
        }

        form {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            color: #d8000c;
            font-weight: bold;
        }

        .message.success {
            color: #155724;
        }
    </style>
</head>
<body>
    <header id="header">
    <div class="container">
      <nav>
        <div class="logo-container">
          <img src="../images/hello.png" alt="Student Portal Logo">
          <span class="logo-text">Admin Dashboard</span>
        </div>
        
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
        
        <div class="nav-links">
            <a href="../Admin_Dashboard/Admin_Dashboard.php">Home</a>
          <a href="../UploadsQuestion/Upload.php">Upload Q uestions</a>
          <a href="../UploadsSolution/UploadsSolution.php">Upload Solutions</a>
          <a href="../UploadNotes/Upload_Notes.php"> Upload Notes</a>
          <a href="../Pages/cicil.html">About Us</a>
          <a href="../Pages/Contact.php">Contact Us</a>
        </div>
      </nav>
    </div>
  </header>


    <form action="" method="POST" enctype="multipart/form-data">
        <h2>Upload Notes</h2>

        <?php if ($message): ?>
            <div class="message <?php echo (strpos($message, '✅') !== false) ? 'success' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <label for="name">Name</label>
        <input type="text" name="name" required>

        <label for="faculty">Faculty</label>
        <input type="text" name="faculty" required>

        <label for="subject">Subject</label>
        <input type="text" name="subject" required>

        <label for="course_type">Course Type</label>
        <select name="course_type" required>
            <option value="Technical">Technical</option>
            <option value="Non-Technical">Non-Technical</option>
        </select>

        <label for="province">Province</label>
        <input type="text" name="province" required>

        <label for="year">Year</label>
        <input type="number" name="year" min="2000" max="2090" required>

        <label for="note_file">Select Note File</label>
        <input type="file" name="note_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>

        <label for="description">Description</label>
        <textarea name="description" rows="4"></textarea>

        <button type="submit" name="upload">Upload Note</button>
    </form>

</body>
</html>
