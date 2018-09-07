<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

class WpmpR_AdminPanel{
    private static $instance;
    private $errors, $messages;
    
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
            self::$instance->actions();
        }
        return self::$instance;
    }
    
    private function actions(){
        ob_start();
        if(!isset($_GET['page_now']) or $_GET['page_now']=='') {
            if(!class_exists('WpmpR_Overview')) {
                $file =  WpmpProductReports::getDir() . '/classes/overview.php';
                require_once $file;
            }
            WpmpR_Overview::getInstance();
        }
        else if(isset($_GET['page_now']) and $_GET['page_now']=='sales') {
            if(!class_exists('WpmpR_Sales')) {
                $file = WpmpProductReports::getDir() . '/classes/sales.php';
                require_once $file;
            }
            WpmpR_Sales::getInstance();
        }
        else if(isset($_GET['page_now']) and $_GET['page_now']=='coupons') {
            if(!class_exists('WpmpR_Coupons')) {
                $file = WpmpProductReports::getDir() . '/classes/coupons.php';
                require_once $file;
            }
            WpmpR_Coupons::getInstance();
        }
        else if(isset($_GET['page_now']) and $_GET['page_now']=='marketplace') {
            if(!class_exists('WpmpR_Marketplace')) {
                $file = WpmpProductReports::getDir() . '/classes/marketplace.php';
                require_once $file;
            }
            WpmpR_Marketplace::getInstance();
        }
        else if(isset($_GET['page_now']) and $_GET['page_now']=='stock') {
            if(!class_exists('WpmpR_Stock')) {
                $file = WpmpProductReports::getDir() . '/classes/stock.php';
                require_once $file;
            }
            WpmpR_Stock::getInstance();
        }
        $content = ob_get_clean();
        echo $this->getHeader();
        echo $content;
        echo $this->getFooter();
    }
    
    private function getHeader(){
        $message = "";
        if(!empty($this->messages)):
            $message = '<div id="" class="updated"><p>';
            foreach($this->messages as $key => $msg):
                $message .= $msg . "<br/>";
            endforeach;
            $message .= "</p></div>";
        endif;
        
        if(!empty($this->errors)):
            $message = '<div id="" class="updated error"><p>';
            foreach($this->errors as $key => $msg):
                $message .= $msg . "<br/>";
            endforeach;
            $message .= "</p></div>";
        endif;
    ?>
        <div class="wrap">
            <h2><?php _e('Advance Report', 'wpmp_report'); ?></h2>
            <?php echo $message; ?>
            <br>
            <div class="wpeden">
                <!-- here code for tab -->
                <div class="container-fluid">
                    
                    <ul id="myTab" class="nav nav-tabs">
                        <li class="<?php if(!isset($_GET['page_now']) || $_GET['page_now'] =='') { echo "active";} ?>">
                            <a href="<?php echo admin_url('/edit.php?post_type=wpdmpro&page=product-reports'); ?>" data-toggle="tab">Overview</a>
                        </li>

                        <li class="<?php if(isset($_GET['page_now']) && $_GET['page_now'] =='sales') { echo "active";} ?>">
                            <a href="<?php echo admin_url('/edit.php?post_type=wpdmpro&page=product-reports&page_now=sales'); ?>" data-toggle="tab">Sales</a>
                        </li>

<!--                        <li class="<?php if(isset($_GET['page_now']) && $_GET['page_now'] =='coupons') { echo "active";} ?>">
                            <a href="<?php echo admin_url('/edit.php?post_type=wpdmpro&page=product-reports&page_now=coupons'); ?>" data-toggle="tab">Coupons</a>
                        </li>

                        <li class="<?php if(isset($_GET['page_now']) && $_GET['page_now'] =='marketplace') { echo "active";} ?>">
                            <a href="<?php echo admin_url('/edit.php?post_type=wpdmpro&page=product-reports&page_now=marketplace'); ?>" data-toggle="tab">Marketplace</a>
                        </li>

                        <li class="<?php if(isset($_GET['page_now']) && $_GET['page_now'] =='stock') { echo "active";} ?>">
                            <a href="<?php echo admin_url('/edit.php?post_type=wpdmpro&page=product-reports&page_now=stock'); ?>" data-toggle="tab">Stock</a>
                        </li>-->
                    </ul>
                        <script type="text/javascript">
                            jQuery(function($){
                               $('#myTab a').click(function (e) {
                                  location.href = $(this).attr('href');
                               }); 
                            });
                            </script>

                            <br>
                            
                
    <?php
    }
    
    private function getFooter(){
        
        
    ?>
                            
    <script type="text/javascript">
        jQuery(function($){
            $('a.Table, a.BarChart, a.PieChart, a.LineChart').click(function(){
                $(this).parent().find('.active').removeClass('active');
                $(this).addClass('active');
                var th = $(this).parent().parent().parent();
                th.find('table,.barchart,.piechart,.linechart').hide();
                var cls = $(this).attr('cls');
                
                th.find('.'+cls).fadeToggle('slow');
                
                var fn = $(this).attr('fn');
                if(fn != '') {
                    // find object
                    var func = window[fn];

                    // is object a function?
                    if (typeof func === "function") func();
                }
                
                
                return false;
            });
        });
    </script>
                </div>
            </div>
        </div>
    <?php
    
    }
    
}

