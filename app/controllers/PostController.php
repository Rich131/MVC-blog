<?php
class PostController {
	private $slimApp;
	private $model;
	private $requestBody;
	public function __construct($model, $action = null, $slimApp, $parameteres = null) {
		$this->model = $model;
		$this->slimApp = $slimApp;
		$this->requestBody = json_decode ( $this->slimApp->request->getBody (), true ); // this must contain the representation of the new user

		if (! empty ( $parameteres ["id"] ))
			$id = $parameteres ["id"];

			switch ($action) {
				case ACTION_GET_POST :
					$this->getPost ( $id );
					break;
				case ACTION_GET_POSTS :
					$this->getPosts ();
					break;
				case ACTION_UPDATE_POST :
					$this->updatePost ( $id, $this->requestBody );
					break;
				case ACTION_CREATE_POST :
					$this->createNewPost ( $this->requestBody );
					break;
				case ACTION_DELETE_POST :
					$this->deletePost ( $id );
					break;
				case ACTION_SEARCH_POSTS :
					$string = $parameteres ["SearchingString"];
					$this->searchPost ( $string );
					break;
				case null :
					$this->slimApp->response ()->setStatus ( HTTPSTATUS_BADREQUEST );
					$Message = array (
							GENERAL_MESSAGE_LABEL => GENERAL_CLIENT_ERROR
					);
					$this->model->apiResponse = $Message;
					break;
			}
	}
	private function getPosts() {
		$answer = $this->model->get();
		if ($answer != null) {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_OK );
			$this->model->apiResponse = $answer;
		} else {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_NOCONTENT );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_NOCONTENT_MESSAGE
			);
			$this->model->apiResponse = $Message;
		}
	}
	private function getPost($postID) {
		$answer = $this->model->getPost ( $postID );
		if ($answer != null) {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_OK );
			$this->model->apiResponse = $answer;
		} else {
				
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_NOCONTENT );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_NOCONTENT_MESSAGE
			);
			$this->model->apiResponse = $Message;
		}
	}
	private function createNewPost($newPost) {
		if ($newID = $this->model->createNewPost ( $newPost )) {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_CREATED );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_RESOURCE_CREATED,
					"post_id" => "$newID"
			);
			$this->model->apiResponse = $Message;
		} else {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_BADREQUEST );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_INVALIDBODY
			);
			$this->model->apiResponse = $Message;
		}
	}
	private function deletePost($postId) {
		//$isSuccessfull = $this->model->deleteUser ( $userId );
		//var_dump($isSuccessfull);
		//die($isSuccessfull);
		if ($this->model->deleteUser ( $postId )) {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_OK );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_RESOURCE_DELETED
			);
			$this->model->apiResponse = $Message;
		} else {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_BADREQUEST );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_ERROR_MESSAGE
			);
			$this->model->apiResponse = $Message;
		}
	}
	private function searchPosts($string) {
		$answer = $this->model->searchUsers ( $string );
		if ($answer != null) {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_OK );
			$this->model->apiResponse = $answer;
		} else {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_NOCONTENT );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_NOCONTENT_MESSAGE
			);
				
			$this->model->apiResponse = $Message;
		}
	}
	private function updatePost($userId, $userDetails) {
		if ($this->model->updateUser ( $userId, $userDetails )) {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_OK );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_RESOURCE_UPDATED,
					"updatedID" => "$userId"
			);
			$this->model->apiResponse = $Message;
		} else {
			$this->slimApp->response ()->setStatus ( HTTPSTATUS_BADREQUEST );
			$Message = array (
					GENERAL_MESSAGE_LABEL => GENERAL_INVALIDBODY
			);
			$this->model->apiResponse = $Message;
		}
	}
}
?>