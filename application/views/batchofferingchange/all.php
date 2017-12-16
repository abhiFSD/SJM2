<div id="filter-template" class="hidden">
    <?php echo $filters ?>
</div>
<div id="wrapper">
    <div id="sidebar-wrapper" class="mobile">
        <form id="searchForm" method="post" class="filter" action="<?php echo site_url('batchofferingchange/manage'); ?>">
            <ul class="sidebar-nav" style="margin-left:0;">
                <li class="sidebar-brand" id="planagrams-mobile-filters" style="">
                    <a href="#menu-toggle"  id="menu-toggle" style="float:right;" > <i class="fa fa-filter" style="font-size:20px !Important;" aria-hidden="true" aria-hidden="true"></i>
                    </a>
                    <h4>Filters</h4>
                </li>
                <li style="height: 250px"> </li>
            </ul>
        </form>
    </div>
    <div class="row1">
        <h3 class="page-header">
            Planagrams
            <span class="pull-right">
                <a href="<?php echo site_url('batchofferingchange/history/'); ?>"  class="btn btn-primary">Planagram History</a>
            </span>
        </h3>
        <form id="searchFormDesktop" method="post" class="desktop filter" action="<?php echo site_url('batchofferingchange/manage'); ?>">
            <ul class="sidebar-nav-desktop" id="planagrams-desktop-filters" >
            </ul>
        </form>
    </div>
    <hr class="zero">
    <div class="row">
        <div class="col-md-12"  id="result">
            <h3 class="text-center">Choose a kiosk to start</h3>
        </div>
    </div>
</div>
<div id="confirm" class="ui-widget">
    <div class="form-group">
        <label>Enter Date & Time </label>
        <input type="datetime-local" id="commit_time" name="commit_time" class="form-control" />
    </div>
</div>
<div id="position-dialog" class="ui-widget">
    <div id="popup-msg"></div>
    <div class="form-group">
        <label>Kiosk:</label>
        <select name="kiosk_name" id="position_kiosk_name" class="filter_crit form-control">
            <option value="">Select</option>
            <?php foreach($kiosks as $kiosk_name) { ?>
            <option value= "<?php echo $kiosk_name->id;?>"><?php echo $kiosk_name->number ." - ". $kiosk_name->location_name; ?></option>
            <?php  } ?>
        </select>
    </div>
    <div class="form-group">
        <label>Position:</label>
        <input type="text" id="position-confirm" name="position" class="form-control" />
    </div>
</div>
<?php $this->load->view('batchofferingchange/modals/modify'); ?>
<?php $this->load->view('batchofferingchange/modals/add_position'); ?>