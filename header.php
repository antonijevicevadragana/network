<?php
require_once "connection.php";
//ako je logovan korisnik postoji sessija
require_once  "validaction.php";

if (isset($_SESSION['id'])) {
    // horizonatlni meni sa poljima: home, profile, connections, logout
    $id = $_SESSION['id'];

    //$img=ProfileImg($conn, $id);

?>
    <header>


        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="myprofile.php"><?php $img = ProfileImg($conn, $id); ?></a> <!--link ka svom profilu(profilna slicica/avatar) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="followers.php">Connections</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reset_password.php">Resset password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="posts.php">Posts</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>
<?php
    //ako nije logovan korisnik home, register, log in
}
if (!isset($_SESSION['username'])) {
?>

    <header>

        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapsibleNavbar">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </header>

<?php




}




?>