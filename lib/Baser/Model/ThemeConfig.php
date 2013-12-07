<?php

App::uses('Imageresizer', 'Vendor');

class ThemeConfig extends AppModel {

/**
 * 画像を保存する
 * 
 * @param array $data
 * @return array
 */
	public function saveImage($data) {

		// TODO インストール時にfilesの書き込み権限チェック＆フォルダ作成

		$saveDir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
		$images = array('logo', 'main_image_1', 'main_image_2', 'main_image_3', 'main_image_4', 'main_image_5');
		$thumbSuffix = '_thumb';
		$old = $this->findExpanded();

		foreach ($images as $image) {
			if (!empty($data['ThemeConfig'][$image]['tmp_name'])) {
				@unlink($saveDir . $old[$image]);
				$pathinfo = pathinfo($old[$image]);
				@unlink($saveDir . $pathinfo['filename'] . $thumbSuffix . $pathinfo['extension']);
				$fileName = $data['ThemeConfig'][$image]['name'];
				$ext = pathinfo($fileName, PATHINFO_EXTENSION);
				$filePath = $saveDir . $image . '.' . $ext;
				$thumbPath = $saveDir . $image . $thumbSuffix . '.' . $ext;
				move_uploaded_file($data['ThemeConfig'][$image]['tmp_name'], $filePath);
				$Imageresizer = new Imageresizer();
				$Imageresizer->resize($filePath, $thumbPath, 320, 320);
				$data['ThemeConfig'][$image] = $image . '.' . $ext;
			} else {
				unset($data['ThemeConfig'][$image]);
			}
		}

		return $data;
	}

	public function deleteImage($data) {

		$saveDir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
		$images = array('logo', 'main_image_1', 'main_image_2', 'main_image_3', 'main_image_4', 'main_image_5');
		$thumbSuffix = '_thumb';
		$old = $this->findExpanded();
		foreach ($images as $image) {
			if (!empty($data['ThemeConfig'][$image . '_delete'])) {
				@unlink($saveDir . $old[$image]);
				$pathinfo = pathinfo($old[$image]);
				@unlink($saveDir . $pathinfo['filename'] . $thumbSuffix . $pathinfo['extension']);
				$data['ThemeConfig'][$image] = '';
			}
		}

		return $data;
	}

	public function updateColorConfig($data) {

		$configPath = getViewPath() . 'css' . DS . 'config.css';
		if (!file_exists($configPath)) {
			return false;
		}
		$File = new File($configPath);
		$config = $File->read();
		$config = str_replace('MAIN', '#' . $data['ThemeConfig']['color_main'], $config);
		$config = str_replace('SUB', '#' . $data['ThemeConfig']['color_sub'], $config);
		$config = str_replace('LINK', '#' . $data['ThemeConfig']['color_link'], $config);
		$config = str_replace('HOVER', '#' . $data['ThemeConfig']['color_hover'], $config);
		$File = new File(WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css', true, 0666);
		$File->write($config);
		$File->close();
	}

}
