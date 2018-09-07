<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

class WpmpR_Coupons{
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
            <h2>MailChimp Feeds 
                <a href="<?php echo "?post_type={$_REQUEST['post_type']}&page={$_REQUEST['page']}&add_new=1" ?>" class="add-new-h2 <?php if(isset($_REQUEST['add_new']) && $_REQUEST['add_new']=='1') { echo 'active';} ?>">Add New</a>
            </h2>
            <?php echo $message; ?>
            <br>
    <?php
    }
    
    private function getFooter(){
        
        
    ?>
        </div>
    <?php
    
    }
    
}

