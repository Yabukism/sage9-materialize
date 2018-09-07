<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

class WpmpR_Sales{
    private static $instance;
    private $var,$currency;
    
    public static function getInstance() {
        if(self::$instance === null && $_REQUEST['page_now']=='sales') {
            self::$instance = new self;
            self::$instance->var = array();
            self::$instance->currency = get_option('_wpmp_curr_sign','$');
            self::$instance->actions();
        }
        return self::$instance;
    }
    
    private function actions(){
        //echo "hello World";
        $this->menu();
        if($_REQUEST['sec']=='by_day' || $_REQUEST['sec']==''){
            $this->salesByDay();
        }
        else if($_REQUEST['sec']=='by_month'){
            $this->salesByMonth();
        }
        else if($_REQUEST['sec']=='product_sales'){
            $this->productSales();
        }
        
    }
    
    private function menu(){
        
        ?>
        <ul class="nav nav-pills">
            <li class="<?php  if($_REQUEST['sec']=='by_day' || $_REQUEST['sec']=='') { echo "active"; }?>"><a href="<?php echo admin_url('edit.php?post_type=wpdmpro&page=product-reports&page_now=sales&sec=by_day'); ?>">Sales By Day</a></li>
            <li class="<?php  if($_REQUEST['sec']=='by_month') { echo "active"; }?>"><a href="<?php echo admin_url('edit.php?post_type=wpdmpro&page=product-reports&page_now=sales&sec=by_month'); ?>">Sales By Month</a></li>
            <li class="<?php  if($_REQUEST['sec']=='product_sales') { echo "active"; }?>"><a href="<?php echo admin_url('edit.php?post_type=wpdmpro&page=product-reports&page_now=sales&sec=product_sales'); ?>">Product Sales</a></li>
        </ul>
        <br><br>
        <?php
    }
    
    private function salesByDay(){
        
        ?>
<div class="row">
    <div class="col-md-12">
        <form class="form-inline" role="form" id="sales_form">
            <?php wp_nonce_field('wpmpr_sales_by_day'); ?>
            <input type="hidden" name="action" value="wpmpr_sales_by_day" />
            <div class="form-group">
              <label class="" for="from">From Date:</label>
              <input type="text" name='from' class="form-control" id="from" readonly="readonly">
            </div>
            <div class="form-group">
              <label class="" for="to">To Date:</label>
              <input type="text" name='to' class="form-control" id="to" readonly="readonly">
            </div>
            <button type="submit" class="btn btn-danger" id="show">Show</button>
        </form>
        <br>
        <div id='error_div'></div>
    </div>
</div>
        
<br><br>

<div style="display:none;" id="graph_row">        
<div class='row'>
    <div class="col-md-3 col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">Total Sales In Range</div>
            <div class="panel-body">
                <h4 id="totalSales"></h4>
            </div>
        </div>
    </div>
    <div class='col-md-3 col-sm-3'>    
        <div class="panel panel-default">
            <div class="panel-heading">Total Orders In Range</div>
            <div class="panel-body">
                <h4 id="totalOrder"></h4>
            </div>
        </div>
    </div>
        
    <div class='col-md-3 col-sm-3'>    
        <div class="panel panel-default">
            <div class="panel-heading">Total Product Count</div>
            <div class="panel-body">
                <h4 id="productCount"></h4>
            </div>
        </div>
    </div>  
</div>
    
<div class='row'>    
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Sales In Range</div>
            <div class="panel-body">
                <div id="sales_graph_2"></div>
            </div>
        </div>
    </div>
</div>
    
</div>

<script type="text/javascript">
    
    
    jQuery(function($){
        $("#from").datepicker({ changeMonth: true, changeYear: true, yearRange: '1900:+0', maxDate:'+0', dateFormat:'yy-mm-dd' });
        $("#to").datepicker({ changeMonth: true, changeYear: true, yearRange: '1900:+0', maxDate:'+0', dateFormat:'yy-mm-dd' });
        
        $('#sales_form').submit(function(){
            $(this).ajaxSubmit({
                dataType: 'json',
                url:'<?php echo admin_url('admin-ajax.php'); ?>',
                success: function(res){
                    console.log(res);
                    //res = json_encode(res);
                    if(res.type=="success"){
                        var str= "";
                        
                        if(res.error){
                           $.each(res.error,function(key,val){
                               str += '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+val+'</div>'
                           });
                           $('#error_div').html(str);
                        }
                        else {
                            $('#error_div').html('');
                            $('#graph_row').fadeIn('slow');
                            
                            if(res.total_income) {
                                $('#totalSales').html('<?php echo $this->currency; ?>' + res.total_income);
                            } else {
                                $('#totalSales').html('<?php echo $this->currency; ?>' + '0');
                            }
                            
                            if(res.total_order) {
                                $('#totalOrder').html(res.total_order);
                            } else {
                                $('#totalOrder').html('0');
                            }
                            
                            if(res.total_product){
                                $('#productCount').html(res.total_product);
                            } else {
                                $('#productCount').html('0');
                            }
                            
                            if(res.byDays){
                                var byDays=[];
                                $.each(res.byDays,function(key,val){
                                    byDays.push([key,val]);
                                });
                                jQuery.jqplot.config.enablePlugins = true;
                                $('#sales_graph_2 div, #sales_graph_2 canvas').remove();
                                plot2 = $.jqplot('sales_graph_2',[byDays], {
                                     title:'Sales By Days',
                                     animate: true,
                                     seriesDefaults:{
                                         renderer:$.jqplot.BarRenderer,
                                         rendererOptions: {
                                             // Set the varyBarColor option to true to use different colors for each bar.
                                             // The default series colors are used.
                                             varyBarColor: true
                                         }
                                     },
                                     axes:{
                                         xaxis:{
                                             renderer: $.jqplot.CategoryAxisRenderer
                                         }
                                     }
                                 });
                            }
                             
                            else{
                                $('#sales_graph_2').html('No record found');
                            }
                                    
                         }
                        
                    }
                    else {
                        alert('Something Went Wrong');
                    }
                }
            });
            return false;
        });
        
        
    });
    
</script>

        <?php
    }
    
    private function salesByMonth(){
        
        ?>
<div class="row">
    <div class="col-md-12">
        <form class="form-inline" role="form" id="sales_form">
            <?php wp_nonce_field('wpmpr_sales_by_month'); ?>
            <input type="hidden" name="action" value="wpmpr_sales_by_month" />
            <div class="form-group">
              <label class="" for="from">Year:</label>
              <select name='year' id='year'>
                  <option value="">Select a year</option>
                    <?php 
                    global $wpdb;
                    $sql = "SELECT MAX(`date`) as max, MIN(`date`) as min FROM `{$wpdb->prefix}ahm_orders";
                    $row = $wpdb->get_row($sql,ARRAY_A);
                    if($row){
                        $start = (int) date('Y',$row['min']);
                        $end = (int) date('Y',$row['max']);
                        for ($i = $start; $i<=$end; $i++) {
                           echo "<option value='$i'>$i</option>"; 
                        }
                    }
                    ?>
              </select>
            </div>
            <button type="submit" class="btn btn-danger" id="show">Show</button>
        </form>
        <br>
        <div id='error_div'></div>
    </div>
</div>
        
<br><br>

<div style="display:none;" id="graph_row">        
<div class='row'>
    <div class="col-md-3 col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">Total Sales For Year</div>
            <div class="panel-body">
                <h4 id="totalSales"></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-3">
        <div class="panel panel-default">
            <div class="panel-heading">Total Orders For Year</div>
            <div class="panel-body">
                <h4 id="totalOrder"></h4>
            </div>
        </div>
    </div>
    
    <div class='col-md-3 col-sm-3'>
        <div class="panel panel-default">
            <div class="panel-heading">Total Product Count</div>
            <div class="panel-body">
                <h4 id="productCount"></h4>
            </div>
        </div>
    </div>
</div>
    
<div class='row'>    
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">Monthly Sales For Year</div>
            <div class="panel-body">
                <div id="sales_graph_2"></div>
            </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    jQuery(function($){
        $('#sales_form').submit(function(){
            $(this).ajaxSubmit({
                dataType: 'json',
                url:'<?php echo admin_url('admin-ajax.php'); ?>',
                success: function(res){
                    console.log(res);
                    //res = json_encode(res);
                    if(res.type=="success"){
                        var str= "";
                        
                        if(res.error){
                           $.each(res.error,function(key,val){
                               str += '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+val+'</div>'
                           });
                           $('#error_div').html(str);
                        }
                        else {
                            $('#error_div').html('');
                            $('#graph_row').fadeIn('slow');
                            
                            if(res.total_income) {
                                $('#totalSales').html('<?php echo $this->currency; ?>' + res.total_income);
                            } else {
                                $('#totalSales').html('<?php echo $this->currency; ?>' + '0');
                            }
                            
                            if(res.total_order) {
                                $('#totalOrder').html(res.total_order);
                            } else {
                                $('#totalOrder').html('0');
                            }
                            
                            if(res.total_product){
                                $('#productCount').html(res.total_product);
                            } else {
                                $('#productCount').html('0');
                            }
                            
                            if(res.byMonth){
                                var byMonth=[];
                                $.each(res.byMonth,function(key,val){
                                    byMonth.push([key,val]);
                                });
                                jQuery.jqplot.config.enablePlugins = true;
                                $('#sales_graph_2 div, #sales_graph_2 canvas').remove();
                                
                                var plot2 = $.jqplot('sales_graph_2',[byMonth], {
                                     title:'Sales By Month',
                                     animate: true,
                                     seriesDefaults:{
                                         renderer:$.jqplot.BarRenderer,
                                         rendererOptions: {
                                             // Set the varyBarColor option to true to use different colors for each bar.
                                             // The default series colors are used.
                                             varyBarColor: true
                                         }
                                     },
                                     axes:{
                                         xaxis:{
                                             renderer: $.jqplot.CategoryAxisRenderer
                                         }
                                     }
                                 });
                             }
                            else{
                                     $('#sales_graph_2').html('<h2>No Record found</h2>');
                            }
                         }
                        
                    }
                    else {
                        alert('Something Went Wrong');
                    }
                }
            });
            return false;
        });
        
        
    });
    
</script>

        <?php
    }
    
    private function productSales(){
        
        ?>
<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" role="form" id="sales_form">
            <?php wp_nonce_field('wpmpr_product_sales'); ?>
            <input type="hidden" name="action" value="wpmpr_product_sales" />
            
            <div class="form-group">
                <label class="col-sm-2 control-label" for="from">From:</label>
                <div class="col-sm-4">
                    <input typ='text' id='from' name='from' class="form-control" readonly="readonly">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label" for="from">To:</label>
                <div class="col-sm-4">
                    <input type='text' id='to' name='to' class="form-control" readonly="readonly">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label" for="from">Product:</label>
                <div class="col-sm-4">
                    <select name="pid" id="ptitle" class="form-control"></select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label" for="from">View:</label>
                <div class="col-sm-4">
                    <select name="view" id="" class="form-control">
                        <option value="1">By Days</option>
                        <option value='2'>By Months</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-danger" id="show">Search</button>
                </div>
            </div>
        </form>
        <br>
        <div id='error_div'></div>
    </div>
</div>
        
<br><br>

<div style="display:none;" id="graph_row">        
<div class='row'>
    <div class="col-md-2 col-sm-2">
        <div class="panel panel-default">
            <div class="panel-heading">Total Sale</div>
            <div class="panel-body">
                <h4 id="totalSales"></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-10 col-sm-10">
        <div class="panel panel-primary">
            <div class="panel-heading">Sales Graph</div>
            <div class="panel-body">
                <div id="sales_graph_1"></div>
            </div>
        </div>
    </div>
</div>
    
<div class='row'>
    <div class="col-md-2 col-sm-2">
        <div class="panel panel-default">
            <div class="panel-heading">Product Count</div>
            <div class="panel-body">
                <h4 id="totalOrder"></h4>
            </div>
        </div>
    </div>
    
    <div class="col-md-10 col-sm-10">
        <div class="panel panel-primary">
            <div class="panel-heading">Product Count Graph</div>
            <div class="panel-body">
                <div id="sales_graph_2"></div>
            </div>
        </div>
    </div>
</div>
        
</div>

<script type="text/javascript">
    jQuery(function($){
        $("#from").datepicker({ changeMonth: true, changeYear: true, yearRange: '1900:+0', maxDate:'+0', dateFormat:'yy-mm-dd' });
        $("#to").datepicker({ changeMonth: true, changeYear: true, yearRange: '1900:+0', maxDate:'+0', dateFormat:'yy-mm-dd' });
        
        jQuery("#ptitle").fcbkcomplete({
            json_url: ajaxurl+"?action=wpmp_autosuggest",
            addontab: true,                   
            maxitems: 1,
            input_min_size: 0,
            //height: 10,
            cache: true,
            newel: false,
            select_all_text: "",
        });
        
        $('#sales_form').submit(function(){
            $(this).ajaxSubmit({
                dataType: 'json',
                url:'<?php echo admin_url('admin-ajax.php'); ?>',
                success: function(res){
                    console.log(res);
                    //res = json_encode(res);
                    if(res.type=="success"){
                        var str= "";
                        
                        if(res.error){
                           $.each(res.error,function(key,val){
                               str += '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+val+'</div>'
                           });
                           $('#error_div').html(str);
                        }
                        else {
                            $('#error_div').html('');
                            $('#graph_row').fadeIn('slow');
                            
                            if(res.totalAmount) {
                                $('#totalSales').html('<?php echo $this->currency; ?>' + res.totalAmount);
                            } else {
                                $('#totalSales').html('<?php echo $this->currency; ?>' + '0');
                            }
                            
                            if(res.totalCount) {
                                $('#totalOrder').html(res.totalCount);
                            } else {
                                $('#totalOrder').html('0');
                            }
                            
                            if(res.productTotal){
                                var productTotal=[];
                                $.each(res.productTotal,function(key,val){
                                    productTotal.push([key,val]);
                                });
                                jQuery.jqplot.config.enablePlugins = true;
                                $('#sales_graph_1 div, #sales_graph_1 canvas').remove();
                                
                                var plot1 = $.jqplot('sales_graph_1',[productTotal], {
                                     title:'Sales Graph',
                                     animate: true,
                                     seriesDefaults:{
                                         renderer:$.jqplot.BarRenderer,
                                         rendererOptions: {
                                             // Set the varyBarColor option to true to use different colors for each bar.
                                             // The default series colors are used.
                                             varyBarColor: true
                                         }
                                     },
                                     axes:{
                                         xaxis:{
                                             renderer: $.jqplot.CategoryAxisRenderer
                                         }
                                     }
                                 });
                             }
                            else{
                                     $('#sales_graph_1').html('<h2>No Record found</h2>');
                            }
                            
                            
                            if(res.productCount){
                                var productCount=[];
                                $.each(res.productCount,function(key,val){
                                    productCount.push([key,val]);
                                });
                                jQuery.jqplot.config.enablePlugins = true;
                                $('#sales_graph_2 div, #sales_graph_2 canvas').remove();
                                
                                var plot2 = $.jqplot('sales_graph_2',[productCount], {
                                     title:'Product Count Graph',
                                     animate: true,
                                     seriesDefaults:{
                                         //renderer:$.jqplot.BarRenderer,
                                         rendererOptions: {
                                             // Set the varyBarColor option to true to use different colors for each bar.
                                             // The default series colors are used.
                                             varyBarColor: true
                                         }
                                     },
                                     axes:{
                                         xaxis:{
                                             renderer: $.jqplot.CategoryAxisRenderer
                                         }
                                     }
                                 });
                             }
                            else{
                                     $('#sales_graph_2').html('<h2>No Record found</h2>');
                            }
                            
                            
                         }
                        
                    }
                    else {
                        alert('Something Went Wrong');
                    }
                }
            });
            return false;
        });
        
        
    });
    
</script>

        <?php
    }
    
    private function salesByCategory(){
        ?>
<div class="row">
    <div class="col-md-12">
        <form class="form-inline" role="form">
            <div class="form-group">
              <label class="" for="from">From Date:</label>
              <input type="text" class="form-control" id="from" readonly="readonly">
            </div>
            <div class="form-group">
              <label class="" for="to">To Date:</label>
              <input type="text" class="form-control" id="to" readonly="readonly">
            </div>
            <button type="submit" class="btn btn-danger">Show</button>
        </form>
    </div>
</div>
        
<br><br>

<div class="row">        

    <div class="col-md-3 col-sm-3">
        <div class="panel panel-info">
            <div class="panel-heading">Total Sales In Range</div>
            <div class="panel-body">
                <h4><?php //echo $this->var['total_products']; ?></h4>
            </div>
        </div>
        
        <div class="panel panel-info">
            <div class="panel-heading">Total Orders In Range</div>
            <div class="panel-body">
                <h4><?php //echo $this->var['total_products']; ?></h4>
            </div>
        </div>
        
        <div class="panel panel-info">
            <div class="panel-heading">Total Product Count</div>
            <div class="panel-body">
                <h4><?php //echo $this->var['total_products']; ?></h4>
            </div>
        </div>
        
        
    </div>
    
    
    <div class="col-md-9">
        <div class="panel panel-info">
            <div class="panel-heading">Sales In Range</div>
            <div class="panel-body">
                <div id="sales_graph">Hello</div>
            </div>
        </div>
    </div>
</div>

        <?php
    }
    
    
    
}

