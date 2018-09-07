<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

class WpmpR_Ajax{
    private static $instance;
    private $var;
    public static function getInstance() {
        if(self::$instance === null) {
            self::$instance = new self;
            self::$instance->var = array();
            self::$instance->actions();
        }
        return self::$instance;
    }
    
    private function actions(){
        add_action('wp_ajax_wpmpr_product_sales',array($this,'productSales'));
        add_action('wp_ajax_wpmpr_sales_by_month',array($this,'salesByMonths'));
        add_action('wp_ajax_wpmpr_sales_by_day',array($this,'salesByDay'));
        
        
    }
    
    public function productSales(){
        $result['type'] = '';
        global $wpdb;
        //print_r($_GET);
        //check nonce
        if(check_ajax_referer( 'wpmpr_product_sales', 'nonce',false )){
            $from = isset($_REQUEST['from']) ? stripslashes($_REQUEST['from']) : '';
            $to = isset($_REQUEST['to']) ? stripslashes($_REQUEST['to']) : '';
            $pid = isset($_REQUEST['pid'][0]) ? stripslashes($_REQUEST['pid'][0]) : '';
            $view = isset($_REQUEST['view']) ? stripslashes($_REQUEST['view']) : '';
            
            $from = strtotime($from);
            $to = strtotime($to);
            
            $flag = false;
            if($pid == '') {
                $result['error'][] = "Please select a product.";
                $flag = true;
            }
            
            if($from == ""){
                $result['error'][] = "Please Select From Date.";
                $error = true;
            }
            if($to == "") {
                $result['error'][] = "Please Select To Date.";
                $error = true;
            }
            
            if($from>$to) {
                $result['error'][] = "Invalid From Date.";
            }
            
            if($flag == false) {
                $sql = "";
            }
            $product  = array();
            $product_count = array();
            $total_amount = 0;
            $total_count = 0;
            
            if($view == "1") {
                $result['view'] = 1;
                $temp = $from;
                while($temp <= $to && count($product) <= 30){
                    $idx = date('M-d',$temp);
                    $product[$idx] = $product_count[$idx] = 0;
                    $temp = strtotime('+1 day',$temp);
                }
                
                $sql = "SELECT `date`,cart_data,total FROM `{$wpdb->prefix}ahm_orders` WHERE `date` >= $from and `date` < $temp and `payment_status`='Completed' ORDER BY `date` asc";
                //echo $sql;
                $results = $wpdb->get_results($sql,ARRAY_A);
                
                
                if($results) {
                    //echo "here";
                    //print_r($results);
                    foreach($results as $row):
                        $cart_data = maybe_unserialize($row['cart_data']);
                        if(!array_key_exists($pid, $cart_data)) continue;
                        //print_r($cart_data);
                        $cart_data = $cart_data[$pid];
                        
                        $idx = date('M-d',$row['date']);
                        $count = 0;
                        $total = 0;
                        if(isset($cart_data['item'])):
                            foreach ($cart_data['item'] as $key => $val):
                                if(!isset($val['coupon_amount']) || $val['coupon_amount'] == "") {
                                    $val['coupon_amount'] = 0;
                                }

                                if(!isset($val['discount_amount']) || $val['discount_amount'] == "") {
                                    $val['discount_amount'] = 0;
                                }
                                if(!isset($val['prices']) || $val['prices'] == "") {
                                    $val['prices'] = 0;
                                }

                                $count += $val['quantity'];
                                $total += number_format((($cart_data['price'] + $val['prices'])*$val['quantity'])-$val['discount_amount'] - $val['coupon_amount'],2,".","");
                            endforeach;

                        else:
                            if(!isset($cart_data['coupon_amount']) || $cart_data['coupon_amount'] == "") {
                                $cart_data['coupon_amount'] = 0;
                            }

                            if(!isset($cart_data['discount_amount']) || $cart_data['discount_amount'] == "") {
                                $cart_data['discount_amount'] = 0;
                            }

                            if(!isset($cart_data['prices']) || $cart_data['prices'] == "") {
                                $cart_data['prices'] = 0;
                            }

                            $count = $cart_data['quantity'];
                            $total = number_format((($cart_data['price'] + $cart_data['prices'])*$cart_data['quantity'])-$cart_data['discount_amount'] - $cart_data['coupon_amount'],2,".","");
                        endif;
                        
                        $product[$idx] += $total;
                        $product_count[$idx] += $count;
                        $total_amount += $total;
                        $total_count += $count;
                        
                        
                    endforeach;
                }
                        

                        
                    
               
                
                
                
            }
            
            else if($view == 2){
                $result['view'] = 2;
                $temp = $from;
                while($temp <= $to) {
                    $idx = date('M-Y',$temp);
                    $product[$idx] = $product_count[$idx] = 0;
                    $temp = strtotime('next month',$temp);
                }
                
                $sql = "SELECT `date`,cart_data,total FROM `{$wpdb->prefix}ahm_orders` WHERE `date` >= $from and `date` < $temp and `payment_status`='Completed' ORDER BY `date` asc";
                //echo $sql;
                $results = $wpdb->get_results($sql,ARRAY_A);
                
                
                if($results) {
                    //echo "here";
                    //print_r($results);
                    foreach($results as $row):
                        $cart_data = maybe_unserialize($row['cart_data']);
                        if(!array_key_exists($pid, $cart_data)) continue;
                        $cart_data = $cart_data[$pid];
                        //print_r($cart_data);
                        $idx = date('M-Y',$row['date']);
                        $count = 0;
                        $total = 0;
                        if(isset($cart_data['item'])):
                            foreach ($cart_data['item'] as $key => $val):
                                if(!isset($val['coupon_amount']) || $val['coupon_amount'] == "") {
                                    $val['coupon_amount'] = 0;
                                }

                                if(!isset($val['discount_amount']) || $val['discount_amount'] == "") {
                                    $val['discount_amount'] = 0;
                                }
                                if(!isset($val['prices']) || $val['prices'] == "") {
                                    $val['prices'] = 0;
                                }

                                $count += $val['quantity'];
                                $total += number_format((($cart_data['price'] + $val['prices'])*$val['quantity'])-$val['discount_amount'] - $val['coupon_amount'],2,".","");
                            endforeach;

                        else:
                            if(!isset($cart_data['coupon_amount']) || $cart_data['coupon_amount'] == "") {
                                $cart_data['coupon_amount'] = 0;
                            }

                            if(!isset($cart_data['discount_amount']) || $cart_data['discount_amount'] == "") {
                                $cart_data['discount_amount'] = 0;
                            }

                            if(!isset($cart_data['prices']) || $cart_data['prices'] == "") {
                                $cart_data['prices'] = 0;
                            }

                            $count = $cart_data['quantity'];
                            $total = number_format((($cart_data['price'] + $cart_data['prices'])*$cart_data['quantity'])-$cart_data['discount_amount'] - $cart_data['coupon_amount'],2,".","");
                        endif;
                        
                        $product[$idx] += $total;
                        $product_count[$idx] += $count;
                        
                        $total_amount += $total;
                        $total_count += $count;
                        
                    endforeach;
                }
            }
            
            $result['productTotal'] = $product;
            $result['productCount'] = $product_count;
            $result['totalAmount'] = $total_amount;
            $result['totalCount'] = $total_count;
            
            $result['type'] = 'success';
        }
        else {
            $result['type'] = "Nonce Error";
        }
        
        
        //echo result 
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
         }
         else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
         }

         die();
    }
    
    public function salesByMonths(){
        $result['type'] = '';
        global $wpdb;
        //check nonce
        if(check_ajax_referer( 'wpmpr_sales_by_month', 'nonce',false )){
            $year = isset($_REQUEST['year']) ? stripslashes($_REQUEST['year']) : '';
            
            if($year == ''){
                $result['error'][] = "Please select a year.";
            }
            else {
                $month = strtotime($year . '-01-01');
                //by month sql quries
                $this->var['byMonth'] = array();
                for ($i = 1; $i <= 12; $i++) {
                    $month2 = strtotime('next month', $month);
                    $sql = "SELECT sum(total) FROM `{$wpdb->prefix}ahm_orders` WHERE `date` >= $month and `date` < $month2 and `payment_status`='Completed' ORDER BY `date`";
                    $idx = date('M-y',$month);
                    $this->var['byMonth'][$idx] = $wpdb->get_var($sql);
                    if(!is_numeric($this->var['byMonth'][$idx])) $this->var['byMonth'][$idx] = 0;
                    //$wpdb->print_error();

                    $month = $month2;
                }
                
                $start = strtotime($year . '-01-01');
                $temp = $year + 1;
                $end = strtotime($temp . '-01-01');
                
                $sql = "SELECT sum(total) as total, count(*) as cnt FROM `{$wpdb->prefix}ahm_orders` WHERE `date`>=$start and `date`<$end and `payment_status`='Completed'";
                $row = $wpdb->get_row($sql,ARRAY_A);
                if($row) {
                    $total = $row['total'];
                    $count = $row['cnt'];
                }
                else {
                    $total = 0;
                    $count = 0;
                }
                
                $result['total_income'] = $total;
                $result['total_order'] = $count;
                $result['byMonth'] = $this->var['byMonth'];
                
            }
            
            
            
            
            
            
            
            
            $result['type'] = 'success';
        }
        else {
            $result['type'] = "Nonce Error";
        }
        
        
        //echo result 
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
         }
         else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
         }

         die();
    }
    
    
    public function salesByDay(){
        $result['type'] = '';
        global $wpdb;
        //check nonce
        if(check_ajax_referer( 'wpmpr_sales_by_day', 'nonce',false )){
            $from = isset($_REQUEST['from']) ? stripslashes($_REQUEST['from']) : '';
            $to = isset($_REQUEST['to']) ? stripslashes($_REQUEST['to']) : '';
            
            $error = false;
            if($from == ""){
                $result['error'][] = "Please Select From Date.";
                $error = true;
            }
            if($to == "") {
                $result['error'][] = "Please Select To Date.";
                $error = true;
            }
            
            if($error == false){
                
                $from = strtotime($from);
                $to = strtotime($to);
                
                $tmp = $from;
                while($tmp <= $to){
                    $idx = date('M d',$tmp);
                    $this->var['byDays'][$idx] = 0;
                    $tmp = strtotime('+1 day', $tmp);
                }

                $sql = "SELECT date,total,cart_data FROM `{$wpdb->prefix}ahm_orders` WHERE `date`>=$from and `date` <= $to and `payment_status`='Completed'";

                $results = $wpdb->get_results($sql, ARRAY_A);
                //$wpdb->print_error();
                $total = 0;
                $total_product = 0;
                $order_count = 0;
                if($results) {
                    
                    foreach ($results as $row):
                        $order_count++;
                        $day = date('M d',$row['date']);
                        $this->var['byDays'][$day] += $row['total'];
                        $total += $row['total'];
                        $total_product += count($row['cart_data']);
                    endforeach;
                }
                //$byDays = '[';
                $byDays = '';
                if($this->var['byDays']) {
                    $count = count($this->var['byDays']);
                    foreach($this->var['byDays'] as $key => $val):
                        $byDays .= "['$key',$val]";
                        $count--;
                        if($count) $byDays .= ', '; 
                    endforeach;
                }
                //$byDays .= ']';
                
                //$result['byDays'] = $byDays;
                $result['byDays'] = $this->var['byDays'];
                $result['total_order'] = $order_count;
                $result['total_income'] = $total;
                $result['total_product'] = $total_product;
            }
            
           
            
            
            
            $result['type'] = 'success';
        }
        else {
            $result['type'] = "Nonce Error";
        }
        
        
        //echo result 
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
         }
         else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
         }

         die();
    }
    
    
    public function ajaxTemplate(){
        $result['type'] = '';
        //check nonce
        if(check_ajax_referer( 'ajax_nonce', 'nonce',false )){
           
           
            
            
            
            $result['type'] = 'success';
        }
        else {
            $result['type'] = "Nonce Error";
        }
        
        
        //echo result 
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
         }
         else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
         }

         die();
    }
}



