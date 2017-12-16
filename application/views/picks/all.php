<style>
select, textarea, input {
    font-size: 14px;
}

.page-header{
  margin-top: 0px;
}
#filter_section select
{
  height: 35px;
}
#single_go_filter {
  margin: 0 0 0 10px;
}
.picklist{
  list-style: none;
  margin-top: 3px;
}

.filter-section-single{
 /*  margin: 5px 0; */
}
@media (max-width: 768px){
 #sidebar-wrapper{
  display: none;
 }
  }
.single-kiosk-table{

  margin: 30px 0;
}
@media (max-width: 768px)
{
#wrapper {
    padding-left: 0 !important;
}

#wrapper {
    padding-left: 0px;

}
table {

      font-size: 1em;
}

}


@media all and  (min-width: 599px) {
 .picklist a div{
  display: inline !important;
}
 .picklist div{
  display: inline !important;
}
.picklist{
  min-width: 220px;
}
}

@media all and  (max-width: 599px) {
.fixwidth{
  width: 100px;
  word-wrap: break-word;
}
table {

      font-size: 1.1em;
}

}
#filter_section ul li .fixwidth{

  width: 100px;

}
</style>


<?php
$today = new DateTime();
 $today_date = $today->format('Y-m-d')."T".$today->format('H:i:s').".000"; ?>

<style type="text/css">

    #filter_section .col-md-4 input[type="text"] {
     margin-left:0px;
    }
 body {
    overflow-x: hidden;
 }


button.multiselect{
  text-align: left;
}
#switchfilter{
    display: none;
}
.apply_filters_button {
    margin: 0 32px 0 0;
}


 .filter_section_mobile ul li {
 list-style: none;
 }
 .filter_section_mobile#filter_section input:not([type=submit]) {
    margin-left: -15px;
    width: auto;
 }
</style>


<div id="wrapper">
    <form method="post" enctype="multipart/form-data">
        <div id="sidebar-wrapper">
            <?php $this->load->view('picks/sections/filters', [
                'label_class' => '', 
                'is_mobile' => false, 
                'states' => $states, 
                'sites' => $sites, 
                'kiosks' => $kiosks,
                'ul_class' => 'sidebar-nav'
            ]); ?>
        </div>
    </form>
    <!-- dialog box ends here -->
    <div class="row">
        <form method="post" enctype="multipart/form-data">
            <div class="col-md-12">
                <h3 class="page-header">Home</h3>
                <div id="filter_section" class="filter_section_mobile">
                    <?php $this->load->view('picks/sections/filters', [
                        'label_class' => 'fixwidth', 
                        'is_mobile' => true, 
                        'states' => $states, 
                        'sites' => $sites, 
                        'kiosks' => $kiosks,
                        'ul_class' => ''
                    ]); ?>
                </div>
                <div class="clearFloat"></div>
            </div>
        </form>
        <div class="filter-row">
            <div class= "filter-section-single">
                &nbsp;
                <div class="btn-group pull-left change_buttons" role="group" aria-label="...">
                    <button role="group" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Generate Picks <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li class="slelect-type" data-type="par" ><a href="javascript:;"  id="par">To PAR</a></li>
                        <li class="slelect-type" data-type="capacity"><a href="javascript:;"  id="capacity">To Capacity</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-12">
                <div id="kiosk_selection" class="single-kiosk-table">
                    <table class="table table-striped  dataTable responsive" id="picks-table">
                        <thead>
                            <tr>
                                <th> <input type="checkbox" name="head_chkbox1" id="head_chkbox1"  class="all-check kiosk-checkbox-all kiosk-checkbox"/></th>
                                <th>Kiosk</th>
                                <th>Open Pick Lists</th>
                            </tr>
                        </thead>
                        <tbody id="attribute-table-content">
                        </tbody>
                    </table>
                </div>
                
                <div id="blank"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>/assets/js/picks.js?20171020"></script>
