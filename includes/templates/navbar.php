
<!--<nav class="navbar navbar-light bg-light">
    <div class = "container">
        <a class="navbar-brand" href="#">Navbar</a>

        <form class="form-inline my-2 my-lg-0 position-relative mr-sm-auto">
            <input class="form-control" type="search" placeholder="Search" aria-label="Search">
            <button class="btn bg-transparent p-0 position-absolute" type="submit" style = "right: 10px;"><i class="fas fa-search"></i></button>
        </form>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Dropdown
                    </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
                </li>
                    <li class="nav-item">
                    <a class="nav-link disabled" href="#">Disabled</a>
                </li>
            </ul>
        </div>
    </div>
</nav>-->

<?php 

    $userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

    $powers = $userPowers["group_id"];

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3 no-print">
    <div class = "container">
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand d-block d-lg-none" href="dashboard.php">منشأة البكارى</a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item <?php if ($pageActive == 'dashboard.php') { echo 'active'; } ?>">
                    <a class="nav-link" href="dashboard.php">الصفحة الرئيسية</a>
                </li>
                <li class="nav-item <?php if ($pageActive == 'students.php') { echo 'active'; } ?>">
                    <a class="nav-link" href="students.php?currentPage=1">بيانات الطلاب</a>
                </li>
                <li class="nav-item <?php if ($pageActive == 'students-activities.php?Activities') { echo 'active'; } ?>">
                    <a class="nav-link" href="students-activities.php?category=Activities&currentPage=1">أنشطة الطلاب</a>
                </li>
                <li class="nav-item <?php if ($pageActive == 'students-activities.php?Irregularities') { echo 'active'; } ?>">
                    <a class="nav-link" href="students-activities.php?category=Irregularities&currentPage=1">مخالفات الطلاب</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    المزيد
                    </a>
                    <div class="dropdown-menu text-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item <?php if ($pageActive == 'activities.php?Activities') { echo 'active'; } ?>" href="activities.php?category=Activities&currentPage=1">قسم الأنشطة</a>
                        <a class="dropdown-item <?php if ($pageActive == 'activities.php?Irregularities') { echo 'active'; } ?>" href="activities.php?category=Irregularities&currentPage=1">قسم المخالفات</a>
                        <?php if ($powers <= 2) { ?><div class="dropdown-divider"></div><?php } ?>
                        <?php if ($powers == 1) { ?><a class="dropdown-item <?php if ($pageActive == 'users.php') { echo 'active'; } ?>" href="users.php">بيانات الأعضاء</a><?php } ?>
                        <?php if ($powers <= 2) { ?><a class="dropdown-item <?php if ($pageActive == 'history.php') { echo 'active'; } ?>" href="history.php"> سجل النشاطات</a><?php } ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">الصفحة الشخصية</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="logout.php">تسجيل الخروج</a>
                    </div>
                </li>
            </ul>
        </div>
        <a class="navbar-brand d-none d-lg-block" href="dashboard.php">منشأة البكارى</a>
    </div>
</nav>