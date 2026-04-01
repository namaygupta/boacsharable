<?php require_once('../config/constants.php'); ?>
<?php
    $selected_date = '';
    $show_popup = false;
    if (isset($_POST['close_btn'])) {
        $_SESSION['popup_closed'] = true;
    }

    if (isset($_SESSION['popup_closed'])) {
        $show_popup = false;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $selected_date = trim($_POST['date'] ?? '');
        $show_popup = true;
    }

    // Support dashboard AJAX row fetch
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && $_POST['id'] === 'submit') {
        $sql = "SELECT * FROM tbl_volunteer";
        $res = mysqli_query($conn, $sql);

        if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
                $id = (int) $row['id'];
                $first_name = htmlspecialchars($row['first_name']);
                $last_name = htmlspecialchars($row['last_name']);
                $email = htmlspecialchars($row['email']);
                $phone_number = htmlspecialchars($row['phone_number']);

                echo "<tr>";
                echo "<td>$first_name</td>";
                echo "<td>$last_name</td>";
                echo "<td>$email</td>";
                echo "<td>$phone_number</td>";
                echo "<td><button class='btn-submit view-full-btn' data-id='$id' style='padding: 0.4rem 0.8rem; font-size: 0.85rem;'>View Full</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No volunteer submissions found.</td></tr>";
        }

        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 90%;
        max-width: 1000px;
        margin: 30px auto;
    }

    h1 {
        margin-bottom: 5px;
    }

    .btn_back {
        display: inline-block;
        margin: 15px 0;
        padding: 8px 14px;
        background-color: #3498db;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .btn_back:hover {
        background-color: #2980b9;
    }

    .error {
        color: red;
        margin-bottom: 15px;
    }

    /* Volunteer cards */
    .volunteer_submission {
        background: white;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .volunteer_submission:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    .volunteer_submission h3 {
        margin: 0 0 5px;
    }

    /* Popup modal */
    .popup {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .popup-content {
        background: white;
        width: 90%;
        max-width: 500px;
        margin: 100px auto;
        padding: 20px;
        border-radius: 10px;
        position: relative;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(-10px);}
        to {opacity: 1; transform: translateY(0);}
    }

    #close_btn {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
    }

    .volunteer_details p {
        margin: 8px 0;
    }

    .check {
            display: flex;
            justify-content: center;
            position: fixed;
            z-index: 990;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
        }

        .check_container {
            background-color: #555;
            margin: auto;
        }

        .check_text {
            color: white;
            font-size: 2rem;
            letter-spacing: 2px;
            margin: 10px 15px;
        }

        .yes_or_no {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin: 10px 15px;
            gap: 10px;
        }

        .confirm_delete {
            background-color: green;
            color: white;
            border: 2px solid green;
        }

        .cancel_delete {
            background-color: red;
            color: white;
            border: 2px solid red;
        }
</style>
    <?php
        if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
    ?>
    <div>
        <?php
            $sql = "SELECT * FROM tbl_volunteer";
            $res = mysqli_query($conn, $sql);
            if ($res) {
                if (mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        $id = $row['id'];
                        $first_name = htmlspecialchars($row['first_name']);
                        $last_name = htmlspecialchars($row['last_name']);
                        $email = htmlspecialchars($row['email']);
                        $phone_number = htmlspecialchars($row['phone_number']);

                        echo "<tr>
                            <td>$first_name</td>
                            <td>$last_name</td>
                            <td>$email</td>
                            <td>$phone_number</td>
                            <td><button class='btn-submit view-full-btn' data-id='$id' style='padding: 0.4rem 0.8rem; font-size: 0.85rem;'>View Full</button></td>
                        </tr>";
                    }
                } else {
                    $_SESSION['error'] = "<div class='error'>No volunteer submissions found.</div>";
                }
            } else {
                $_SESSION['error'] = "<div class='error'>Error getting data</div>";
            }
        ?>
        
    </div>
    <div class="volunteer_delete"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const popup = document.getElementById('popup');
            const closeBtn = document.getElementById('close_btn');

            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    popup.style.display = 'none';
                });
            }

            // Delegated click for all current/future View Full buttons
            document.addEventListener('click', (event) => {
                const btn = event.target.closest('.view-full-btn');
                if (btn) {
                    const volunteerId = btn.dataset.id;
                    showVolunteerDetails(volunteerId);
                }
            });
        });

        function showVolunteerDetails(volunteerid) {
            const popup = document.getElementById('popup');

            
            fetch('get_volunteer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(volunteerid)
            })
            .then(res => res.text())
            .then(data => {
                const detailsEl = document.querySelector('.volunteer_details');
                if (detailsEl) {
                    detailsEl.innerHTML = data;
                    popup.style.display = 'block';
                }
            })
            .catch(err => {
                console.error('Error loading volunteer details:', err);
            });
        }

        function deleteVolunteer(id) {
            const volunteerid = id.split('-')[1];
            fetch('delete_volunteer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + encodeURIComponent(volunteerid)
            })
            .then(res => res.text())
            .then(data => {
                document.querySelector('.volunteer_delete').innerHTML = data;
                const popup = document.getElementById('popup');
                if (popup) popup.style.display = 'block';
            });
        }
    </script>

    <div id="popup" class="popup" <?php echo $show_popup ? 'style="display:block;"' : ''; ?> >
        <form action="" method="POST" enctype="multipart/form-data" class="popup-content">
            <button type="button" id="close_btn">&times;</button>
            <div class="volunteer_details">
            </div>
        </form>
    </div>
</body>
</html>