<?php

class ResponseBuilder {
	
		public function getErrorResponse($message) {
			return "{'error': '{$message}'}";
		}
		
		public function getSuccessResponse() {
			return "{'success': 'true'}";
		}
} 

?>