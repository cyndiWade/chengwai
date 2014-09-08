<?php

class PublicAction extends HomeBaseAction {
	

	public function verify(){
		import('@.ORG.Util.Image');
		Image::buildImageVerify();
	}
}

?>