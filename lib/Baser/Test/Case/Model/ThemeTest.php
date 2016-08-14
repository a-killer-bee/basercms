<?php
/**
 * Themeモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('Theme', 'Model');

/**
 * ThemeTest class
 * 
 * class NonAssosiationTheme extends Theme {
 *  public $name = 'Theme';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class ThemeTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.ThemeConfig',
	);

	public function setUp() {
		parent::setUp();
		$this->Theme = ClassRegistry::init('Theme');
	}

	public function tearDown() {
		unset($this->Theme);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->Theme->create(array(
			'Theme' => array(
				'name' => '',
			)
		));
		$this->assertFalse($this->Theme->validates());
		$this->assertArrayHasKey('name', $this->Theme->validationErrors);
		$this->assertEquals('テーマ名を入力してください。', current($this->Theme->validationErrors['name']));
	}

	public function test半角英数チェック正常系() {
		$this->Theme->create(array(
			'Theme' => array(
				'name' => '123abc',
				'url' => 'http://abc.jp',
				'old_name' => 'hoge'
			)
		));
		$this->assertTrue($this->Theme->validates());
	}

	public function test半角英数チェック異常系() {
		$this->Theme->create(array(
			'Theme' => array(
				'name' => '１２３ａｂｃ',
				'url' => 'http://ａｂｃ.jp',
				'old_name' => 'hoge'
			)
		));
		$this->assertFalse($this->Theme->validates());
		$this->assertArrayHasKey('name', $this->Theme->validationErrors);
		$this->assertEquals('テーマ名は半角英数字、ハイフン、アンダーバーのみで入力してください。', current($this->Theme->validationErrors['name']));
		$this->assertArrayHasKey('url', $this->Theme->validationErrors);
		$this->assertEquals('URLは半角英数字のみで入力してください。', current($this->Theme->validationErrors['url']));
	}

	public function testURLチェック異常系() {
		$this->Theme->create(array(
			'Theme' => array(
				'url' => 'hoge',
				'old_name' => 'hoge'
			)
		));
		$this->assertFalse($this->Theme->validates());
		$this->assertArrayHasKey('url', $this->Theme->validationErrors);
		$this->assertEquals('URLの形式が間違っています。', current($this->Theme->validationErrors['url']));
	}

	public function test重複チェック異常系() {
		$this->Theme->create(array(
			'Theme' => array(
				'name' => 'nada-icons',
				'old_name' => 'hoge',
			)
		));
		$this->assertFalse($this->Theme->validates());
		$this->assertArrayHasKey('name', $this->Theme->validationErrors);
		$this->assertEquals('既に存在するテーマ名です。', current($this->Theme->validationErrors['name']));
	}

/**
 * 保存
 */
	public function testSaveOnRename() {
		$path = WWW_ROOT . 'theme' . DS;
		$data = array('Theme' => array(
			'old_name' => 'nada-icons',
			'name' => 'new-nada-icons',
			)
		);
		$this->Theme->save($data);
		$this->assertFileExists($path . 'new-nada-icons', 'ファイル名を変更できません');
		$Folder = new Folder($path . 'new-nada-icons');
		$Folder->move(['to' => $path . 'nada-icons']);
	}

}
