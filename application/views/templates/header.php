<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <link rel="shortcut icon" href="<?php print base_url(); ?>favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?php print base_url(); ?>apple-touch-icon.png" sizes="57x57">

    <title>AROMA</title>

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href='//fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>
    <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/animate.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/offline-language-english.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/offline-language-english-indicator.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/offline-theme-default-indicator.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/nav.css?<?php print $this->config->item('version'); ?>">
    <link href="<?php echo base_url(); ?>assets/js/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/fine-uploader/fine-uploader-gallery.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/1.0.7/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/assets/css/bootstrap-multiselect.css?2017">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <!-- Latest compiled and minified JavaScript -->
    <!--[if lt IE 10]>
    <script src="https://test.catapultian.org/assets/js/ie.js"></script>
    <![endif]-->

    <!-- Latest compiled and minified JavaScript -->

    <script src="https://code.jquery.com/jquery-2.0.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>
    <script src="https://cdn.datatables.net/responsive/1.0.7/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/natural.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/offline.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap-notify.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/tmpl.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jqBootstrapValidation.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/bootstrap-multiselect.js"></script>
    
    <script src="<?php echo base_url(); ?>assets/js/app.js?<?php print $this->config->item('version'); ?>"></script>

    <script>
        var appPath = "<?php echo site_url(); ?>";
    </script>
    <style>
        @media screen and (-webkit-min-device-pixel-ratio:0) {
            select,
            textarea,
            input {
                font-size: 16px;
            }
        }

    </style>
    <![endif]-->
</head>
<body id="<?php if (!empty($body_id)) print $body_id; ?>">
<?php
$loggedInUser = $this->session->userdata('user_id');
$role_id = $this->session->userdata('role_id');
?>
<div class="container">
    <div class="cols-md-12">
        <a class="desktop" href="<?PHP echo base_url () ?>"><img style="margin-bottom:10px" src="<?php echo base_url () ?>assets/images/aroma_reduced.jpg" /></a>

        <?php if (! (isset($disableMenu) && $disableMenu) ) {?>
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->

                    <div class="navbar-header">

                        <div class="mobile_logo_holder">
                            <a class="mobile" href="<?PHP echo base_url () ?>"><img  src="<?php echo base_url () ?>assets/images/aroma_reduced.jpg" /></a>
                        </div>

                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                        <ul class="nav navbar-nav mobile mobile-menu" role="menu">
                            <?php if ($role_id < 3): ?>
                                <li style="border: none;"><a href="<?php echo site_url("stockmovement/jobs"); ?>" class="current">Current Jobs</a></li>
                                <li><a href="<?php echo site_url('configitem/batchHistory'); ?>" class="current">Kiosk Attributes</a></li>
                                <li><a href="<?php echo site_url('batchofferingchange/all'); ?>" class="current">Planagrams</a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo site_url('stocktakenew'); ?>" class="current">Stocktake</a></li>
                            <li><a href="<?php echo site_url('stockadjustmentnew'); ?>" class="current">Stock Adjustment</a></li>
                            <li><a href="<?php echo site_url('stockmovement/listall'); ?>" class="current">Stock Movement Log</a></li>
                            <li><a class="logoutlink" href="<?php echo site_url('auth/logout'); ?>">Logout</a></li>
                        </ul>

                        <ul class="nav navbar-nav desktop">
                            <?php if ($role_id < 4) { ?>
                                <li> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Field Ops<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo site_url("stockmovement/jobs"); ?>" class="current">Current Jobs</a></li>
                                        <?php if ($role_id < 2): ?>
                                            <li><a href="<?php echo site_url('stockmovement/completed_jobs'); ?>" class="current">Job History</a></li>
                                            <li><a href="<?php echo site_url(''); ?>" class="current" style="color: #999999">Ops Dashboard</a></li>
                                            <li><a href="<?php echo site_url(''); ?>" class="current" style="color: #999999">Cash Handling</a></li>
                                            <li><a href="<?php echo site_url(''); ?>" class="current" style="color: #999999">Kiosk Photos</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($role_id < 4) { ?>
                                <li> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Kiosks<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">

                                        <li><a href="<?php echo site_url('batchofferingchange/all'); ?>" class="current">Planagrams</a></li>
                                        <?php if ($role_id < 2) { ?>
                                        <li><a href="<?php echo site_url('batchofferingchange/history'); ?>" class="current">Planagram History</a></li>
                                        <?php } ?>
                                        <li><a href="<?php echo site_url('configitem/batchHistory'); ?>" class="current">Kiosk Attributes</a></li>
                                        <?php if ($role_id < 3) { ?>
                                            <li><a href="<?php echo site_url('kiosk/all'); ?>" class="current">Kiosks</a></li>
                                            <li><a href="<?php echo site_url('kioskmodel/all'); ?>">Kiosk Models</a></li>
                                        <?php if ($role_id < 2) { ?>
                                        <li><a href="<?php echo site_url(''); ?>" class="current" style="color: #999999">Kiosk Alerts</a></li>
                                        <li><a href="<?php echo site_url('configitem/all'); ?>" class="current">Kiosk Attribute Fields</a></li>
                                        <?php } ?>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($role_id < 4) { ?>
                                <li> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Sites<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo site_url('deployment/all'); ?>">Deployments</a></li>
                                        <?php if ($role_id < 3) { ?>
                                        <li><a href="<?php echo site_url('kiosklocation/all'); ?>">Kiosk Locations</a></li>
                                        <li><a href="<?php echo site_url('site/all'); ?>">Kiosk Sites</a></li>
                                        <li><a href="<?php echo site_url('licensor/all'); ?>">Licensors</a></li>
                                        <!--<li><a href="<?php /*echo site_url('agreement/all'); */?>">License Agreements</a></li>-->
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($role_id < 4) { ?>
                            <li> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Warehousing<span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <?php if ($role_id < 3) { ?>
                                    <li><a href="<?php echo site_url('inventorylocation/all'); ?>">Inventory Locations</a></li>
                                    <li><a href="<?php echo site_url('products/assignskunew'); ?>">Assign SKUs</a></li>
                                    <?php } ?>
                                    <li><a href="<?php echo site_url('stocktakenew'); ?>" class="current">Stocktake</a></li>
                                    <li><a href="<?php echo site_url('stockadjustmentnew'); ?>" class="current">Stock Adjustment</a></li>
                                    <li><a href="<?php echo site_url('stocktakenew/totalinventory'); ?>" class="current">Stock on Hand</a></li>
                                    <li><a href="<?php echo site_url('stocktakenew/listall'); ?>" class="current">Inventory By Warehouse</a></li>
                                    <li><a href="<?php echo site_url('stockmovement/listall'); ?>" class="current">Stock Movement Log</a></li>
                                </ul>
                            </li>
                            <?php } ?>

                            <?php if ($role_id < 4) { ?>
                                <li> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Items<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo site_url('products/all/'); ?>">Items</a></li>
                                        <?php if ($role_id < 2) { ?>
                                        <li><a href="<?php echo site_url('productattribute/all'); ?>" class="current">Item Attribute Fields</a></li>
                                        <li><a href="<?php echo site_url('productcategory/all'); ?>">Item Categories</a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($role_id < 4) { ?>
                                <li> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Settings<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php if ($role_id < 2) { ?>
                                        <li><a href="<?php echo site_url(''); ?>" class="current" style="color: #999999">My Profile</a></li>
                                        <li><a href="<?php echo site_url('user/all'); ?>">Users</a></li>
                                        <?php } ?>
                                        <li><a class="logoutlink" href="<?php echo site_url('auth/logout'); ?>">Logout</a></li>
                                    </ul>
                                </li>
                            <?php } ?>

                        </ul>

                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        <?php } ?>
    </div>
    <div id="global-messages">
        <?php $this->load->view('templates/messages'); ?>
    </div>
</div>
<div class="container">
