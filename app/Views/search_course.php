<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berbaring | List of Course</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/bootstrap.css">

    <link rel="stylesheet" href="/assets/vendors/iconly/bold.css">
    <link rel="stylesheet" href="/assets/vendors/jquery-datatables/jquery.dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/vendors/fontawesome/all.min.css">

    <link rel="stylesheet" href="/assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="shortcut icon" href="/assets/images/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/card.css">
    <link rel="stylesheet" href="/assets/css/responsive-img.css">

</head>

<body>
    <div id="app">
        <!--pastiin include global nav di semua page! -->
        <?php include('global_nav.php'); ?>
        <div id="main" class="layout-horizontal">

            <div class="content-wrapper container" style="padding-top: 60px;">

                <div class="page-heading">
                    <h3>Result for "<?= $query ?>"</h3>
                </div>
                <div class="page-content">
                    <!-- Basic Tables start -->
                    <section class="content-types">
                        <?php if ($course) : ?>
                            <div class="row row-cols-3 row-cols-md-4 g-4">
                                <?php foreach ($course as $c) : ?>
                                    <div class="col">
                                        <a href="<?= base_url('course/' . $c['c_id']); ?>">
                                            <div class="card" style="max-height: 100vw">
                                                <div class="card-content">
                                                    <img src="<?= '/uploads' . '/' . $c['c_id'] . '/' . $c['c_imagepath'] ?>" class="card-img-top img-fluid" alt="">
                                                    <div class="card-body">
                                                        <?php if (strlen($c['c_name']) > 22) {
                                                            $judul = substr($c['c_name'], 0, 22);
                                                            $judul .= "...";
                                                        } else {
                                                            $judul = $c['c_name'];
                                                        }
                                                        ?>
                                                        <h5 class="card-title"><?= $judul ?></h5>
                                                        <p class="card-text"><?= $c['name'] ?></p>
                                                    </div>
                                                    <div class="card-footer border-0">
                                                        <h6 class="card-text" style="color:#409CA6">
                                                            <?php if ($c['c_price'] == 0) : ?>
                                                                FREE
                                                            <?php else : echo 'Rp' . number_format($c['c_price'], 0, ',', '.') ?>
                                                        </h6>
                                                    <?php endif ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php else : ?>
                            <div class="jus">
                                <img style="padding-bottom: 1em;" src="/assets/images/no_data.png" class="responsive-img" alt="NoData" srcset="">
                                <h3>Bruh, "<?= $query ?>" gak ditemukan :(</h3>
                            </div>
                        <?php endif ?>
                    </section>
                </div>
            </div>
        </div>
        <?php include('footer.php') ?>
        <!-- <footer>
            <div class="container">
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2021 &copy; Mazer</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a href="http://ahmadsaugi.com">A. Saugi</a></p>
                    </div>
                </div>
            </div>
        </footer> -->
    </div>
    </div>
    <script src="/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>

    <script src="/assets/vendors/apexcharts/apexcharts.js"></script>
    <script src="/assets/js/pages/dashboard.js"></script>

    <script src="/assets/js/pages/horizontal-layout.js"></script>
    <script src="/assets/vendors/jquery/jquery.min.js"></script>
    <script src="/assets/vendors/jquery-datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/vendors/jquery-datatables/custom.jquery.dataTables.bootstrap5.min.js"></script>
    <script src="/assets/vendors/fontawesome/all.min.js"></script>
</body>

</html>