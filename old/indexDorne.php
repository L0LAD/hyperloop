<?php

    $conn = new mysqli("localhost", "root", "", "hyperloop");
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué: " . $conn->connect_error);
    }

    $reqNameStop = "SELECT * FROM stop ORDER BY name";
    $ansNameStart = $conn->query($reqNameStop);
    $ansNameEnd = $conn->query($reqNameStop);

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('head.php'); ?>

  <body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="dorne-load"></div>
    </div>

    <!-- ***** Welcome Area Start ***** -->
    <section class="dorne-welcome-area bg-img bg-overlay" style="background-image: url(img/bg-img/hero-1.jpg);">
        <div class="container h-50">
            <div class="row h-100 align-items-center justify-content-center">
                <div class="col-12 col-md-10">
                    <div class="hero-content">
                        <h2>Discover places near you</h2>
                        <h4>This is the best guide of your city</h4>
                    </div>
                    <div class="hero-search-form">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-places" role="tabpanel" aria-labelledby="nav-places-tab">
                                <h6>What are you looking for?</h6>
                                <form action="/hyperloop/search.php" method="post">
                                    <select id="startSelect" class="custom-select" name="startSelect" onChange="fields_valid()">
                                        <option value="empty"></option>
                                        <?php
                                        while ($stop = $ansNameStart->fetch_assoc()) {
                                            $name = $stop["name"];
                                            echo "<option value='$name'>$name</option>";
                                        } ?>
                                    </select>
                                    <select id="endSelect" class="custom-select" name="endSelect" onChange="fields_valid()">
                                        <option value="empty"></option>
                                        <?php
                                        while ($stop = $ansNameEnd->fetch_assoc()) {
                                            $name = $stop["name"];
                                            echo "<option value='$name'>$name</option>";
                                        } ?>
                                    </select>
                                    <button id="searchButton" type="submit" class="btn dorne-btn" disabled><i class="fa fa-search pr-2"></i> Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
    <!-- ***** Welcome Area End ***** -->

    <!-- ****** Footer Area Start ****** -->
    <footer class="dorne-footer-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 d-md-flex align-items-center justify-content-between">
                    <div class="footer-text">
                        <p>Developped by L0LAD</p>
                    </div>
                    <div class="footer-social-btns">
                        <a href="#"><i class="fa fa-linkedin"></i></a>
                        <a href="#"><i class="fa fa-facebook"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- ****** Footer Area End ****** -->

    <!-- JavaScript -->
    <script type="text/javascript">
        function fields_valid() {
            var startSelect = document.getElementById('startSelect');
            var start = startSelect.options[startSelect.selectedIndex].value;

            var endSelect = document.getElementById('endSelect');
            var end = endSelect.options[endSelect.selectedIndex].value;

            if (start!='empty' && end!='empty') {
                document.getElementById('searchButton').disabled = false;
            } else {
                document.getElementById('searchButton').disabled = true;
            }
        }
    </script>

    <!-- JavaScript optionnels -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/popper.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/others/plugins.js"></script>
    <script src="js/active.js"></script>
</body>

</html>