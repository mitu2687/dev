<?php
class UserMasterController extends AppController {

public function myroom(){
$this->autoRender = false;
print_r($this->Cookie);
}

}
