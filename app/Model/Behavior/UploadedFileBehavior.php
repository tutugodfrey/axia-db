<?php
App::uses('ModelBehavior', 'Model');

class UploadedFileBehavior extends ModelBehavior {

/**
 * moveUploadedFile
 *
 * @param Model $model Model class
 * @param string $uploadedFileName uploaded file fullpath
 * @param string $targetFolder new file location
 * @param string $filename new file name
 * @return bool $response
 */
	public function moveUploadedFile($model, $uploadedFilePath, $targetFolder, $filename) {
		if (!is_dir($targetFolder)) {
			mkdir($targetFolder, 0775);
		}
		$response = move_uploaded_file($uploadedFilePath, $targetFolder . $filename);
		return $response;
	}
}
