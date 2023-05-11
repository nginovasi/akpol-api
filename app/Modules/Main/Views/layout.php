<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Blank | Basik - Bootstrap 4 Web Application</title>
        <meta name="description" content="Responsive, Bootstrap, BS4" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <!-- style -->
        <!-- build:css ../assets/css/site.min.css -->
        <link rel="stylesheet" href="<?=base_url()?>/assets/css/bootstrap.css" type="text/css" />
        <link rel="stylesheet" href="<?=base_url()?>/assets/css/theme.css" type="text/css" />
        <link rel="stylesheet" href="<?=base_url()?>/assets/css/style.css" type="text/css" />
        <link rel="stylesheet" href="<?=base_url()?>/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" type="text/css" />
        <link rel="stylesheet" href="<?=base_url()?>/assets/libs/select2/dist/css/select2.min.css" type="text/css" />
        <!-- jQuery -->
        <script src="<?=base_url()?>/assets/libs/jquery/dist/jquery.min.js"></script>
        <!-- endbuild -->
        <style type="text/css">
            .btn-icon-datatable {
                width: 2.125rem;
                height: 2.125rem;
                padding: 7px;
                margin: 0 3px;
            }

            .column-2action {
                width: 100px !important;
            }
        </style>
    </head>
    <body class="layout-row">

    	<?php echo view('App\Modules\Main\Views\menu'); ?>

        <div id="main" class="layout-column flex">

            <?php echo view('App\Modules\Main\Views\navbar'); ?>

            <!-- ############ Content START-->
            <div id="content" class="flex ">
                <!-- ############ Main START-->
                <?php if(isset($load_view)){ 
                    echo view($load_view);
                } else { 
                    echo view('App\Modules\Main\Views\dashboard');
                } ?>
                <!-- ############ Main END-->
            </div>
            <!-- ############ Content END-->

            <?php echo view('App\Modules\Main\Views\footer'); ?>
        </div>
        <!-- build:js ../assets/js/site.min.js -->
        <!-- Bootstrap -->
        <script src="<?=base_url()?>/assets/libs/popper.js/dist/umd/popper.min.js"></script>
        <script src="<?=base_url()?>/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- ajax page -->
        <!-- <script src="<?=base_url()?>assets/libs/pjax/pjax.min.js"></script> -->
        <!-- <script src="<?=base_url()?>assets/js/ajax.js"></script> -->
        <!-- lazyload plugin -->
        <script src="<?=base_url()?>/assets/js/lazyload.config.js"></script>
        <script src="<?=base_url()?>/assets/js/lazyload.js"></script>
        <script src="<?=base_url()?>/assets/js/plugin.js"></script>
        <!-- scrollreveal -->
        <script src="<?=base_url()?>/assets/libs/scrollreveal/dist/scrollreveal.min.js"></script>
        <!-- feathericon -->
        <script src="<?=base_url()?>/assets/libs/feather-icons/dist/feather.min.js"></script>
        <script src="<?=base_url()?>/assets/js/plugins/feathericon.js"></script>
        <!-- theme -->
        <script src="<?=base_url()?>/assets/js/theme.js"></script>
        <script src="<?=base_url()?>/assets/js/utils.js"></script>
        <!-- endbuild -->
        <script src="<?=base_url()?>/assets/libs/datatables/media/js/jquery.dataTables.min.js"></script>
        <script src="<?=base_url()?>/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?=base_url()?>/assets/libs/select2/dist/js/select2.full.min.js"></script>
        <script src="<?=base_url()?>/assets/js/plugins/sweetalert.js"></script>
    </body>
</html>