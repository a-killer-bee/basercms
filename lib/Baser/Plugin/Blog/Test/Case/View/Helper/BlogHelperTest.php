<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Test.Case
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcAppView', 'View');
App::uses('Helper', 'View');
App::uses('HtmlHelper', 'View.Helper');
App::uses('BcTimeHelper', 'View.Helper');
App::uses('BlogHelper', 'Blog.View/Helper');
App::uses('BlogPost', 'Blog.Model');
App::uses('BlogContent', 'Blog.Model');
App::uses('BlogCategory', 'Blog.Model');

/**
 * Blog helper library.
 *
 * @package Baser.Test.Case
 * @property BlogPost $BlogPost
 * @property BlogContent $BlogContent
 * @property BlogHelper $Blog
 */
class BlogHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'plugin.blog.View/Helper/BlogBaserHelper/BlogCategoryTree',	// テスト内で読み込む
		'plugin.blog.Model/BlogTag/BlogPostBlogTagFindCustomPrams',	// テスト内で読み込む
		'plugin.blog.Model/BlogTag/BlogPostsBlogTagBlogTagFindCustomPrams',	// テスト内で読み込む
		'plugin.blog.Model/BlogTag/BlogTagBlogTagFindCustomPrams',	// テスト内で読み込む
		'plugin.blog.Model/BlogTag/BlogContentBlogTagFindCustomPrams',	// テスト内で読み込む
		'plugin.blog.Model/BlogTag/ContentBlogTagFindCustomPrams',	// テスト内で読み込む
		'plugin.blog.View/Helper/BlogBaserHelper/BlogPostBlogBaserHelper',// テスト内で読み込む
		'baser.Default.BlogPostsBlogTag',// テスト内で読み込む
		'plugin.blog.View/Helper/BlogBaserHelper/ContentMultiBlog',	// テスト内で読み込む
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Page',
		'baser.Default.Plugin',
		'baser.Default.BlogComment',
		'baser.Default.BlogContent',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.BlogTag',
		'plugin.blog.Model/BlogPost/BlogCategoryModel',
		'plugin.blog.Model/BlogPost/BlogPostModel',
		'plugin.blog.Model/BlogPost/BlogPostsBlogTagModel',
	);

/**
 * View
 * 
 * @var View
 */
	protected $_View;

/**
 * __construct
 * 
 * @param string $name
 * @param array $data
 * @param string $dataName
 */
	public function __construct($name = null, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
	}

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->View = new BcAppView();
		$this->View->request->params['Site'] = array(
			'use_subdomain' => null,
			'name' => null,
			'alias' => null,
		);
		$this->View->request->params['Content'] = [
			'url' => '/news/',
			'name' => 'news',
			'title' => '新着情報'
		];
		$this->Blog = new BlogHelper($this->View);

		$this->BlogContent = ClassRegistry::init('Blog.BlogContent');
		$this->BlogContent->expects(array());
		$this->Blog->setContent(1);
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Blog);
		unset($this->BlogContent);
		Router::reload();
		parent::tearDown();
	}

/**
 * ブログコンテンツデータをセットする
 * 
 * @param int $blogContentId ブログコンテンツID
 * @param bool $viewVars viewVarsを設定
 * @dataProvider setContentDataProvider
 */
	public function testSetContent($blogContentId, $viewVars, $expected) {
		if ($viewVars) {		
			$View = new View();
			$View->viewVars = array('blogContent' => array(
				'BlogContent' => array(
					'id' => 3,
					'name' => 'test',
				)
			));
			$this->Blog = new BlogHelper($View);
		}
		$this->Blog->blogContent = null;
		$this->Blog->setContent($blogContentId);
		$this->assertEquals($this->Blog->blogContent['id'], $expected, 'ブログコンテンツデータを正しくセットできません');
	}

	public function setContentDataProvider() {
		return array(
			array(null, false, null),
			array(2, false, 2),
			array(null, true, 3),
		);
	}

/**
 * ブログIDを取得する
 */
	public function testGetCurrentBlogId() {
		$result = $this->Blog->getCurrentBlogId();
		$expects = '1';
		$this->assertEquals($expects, $result, 'ブログIDを正しく取得できません');
	}

/**
 * ブログアカウント名を取得する
 */
	public function testGetBlogName() {
		$result = $this->Blog->getBlogName();
		$expects = 'news';
		$this->assertEquals($expects, $result, 'ブログアカウント名を正しく取得できません');
	}

/**
 * タイトルを取得する
 */
	public function testGetTitle() {
		$result = $this->Blog->getTitle();
		$expects = '新着情報';
		$this->assertEquals($expects, $result, 'タイトルを正しく取得できません');
		
	}

/**
 * ブログの説明文を取得する
 */
	public function testGetDescription() {
		$result = $this->Blog->getDescription();
		$expects = 'baserCMS inc. [デモ] の最新の情報をお届けします。';
		$this->assertEquals($expects, $result, 'ブログの説明文を正しく取得できません');
	}

/**
 * 記事タイトルを取得する
 */
	public function testGetPostTitle() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 2,
			'name' => 'test-name',
			'no' => 4,
		));

		// $link = true
		$result = $this->Blog->getPostTitle($post);
		$this->assertEquals('<a href="/news/archives/4">test-name</a>', $result, '記事タイトルを正しく取得できません');

		// $link = false
		$result  = $this->Blog->getPostTitle($post, false);
		$this->assertEquals('test-name', $result, '記事タイトルを正しく取得できません');
	}

/**
 * 記事へのリンクを取得する
 */
	public function testGetPostLink() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 2,
			'no' => 3,
		));
		$result = $this->Blog->getPostLink($post, 'test-title');
		$this->assertEquals('<a href="/news/archives/3">test-title</a>', $result, '記事へのリンクを正しく取得できません');
	}

/**
 * ブログ記事のURLを取得する
 */
	public function testGetPostLinkUrl() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 2,
			'no' => 3,
		));
		$result = $this->Blog->getPostLinkUrl($post);
		$this->assertEquals('/news/archives/3', $result, '記事へのリンクを正しく取得できません');
	}

/**
 * 記事の本文を取得する
 *
 * @param bool $moreText 詳細データを表示するかどうか
 * @param bool $moreLink 詳細ページへのリンクを表示するかどうか
 * @param mixed $cut 文字をカットするかどうかを真偽値で指定。カットする場合、文字数を数値で入力
 * @param string $expected
 * @dataProvider getPostContentDataProvider
 */
	public function testGetPostContent($moreText, $moreLink, $cut, $expected) {
		$post = array('BlogPost' => array(
			'content' => 'test-content',
			'detail' => 'test-detail',
			'no' => 3
		));
		$result = $this->Blog->getPostContent($post, $moreText, $moreLink, $cut);
		$this->assertEquals($result, $expected, '記事の本文を正しく取得できません');
	}

	public function getPostContentDataProvider() {
		return array(
			array(true, false, false, '<div class="post-body">test-content</div><div id="post-detail">test-detail</div>'),
			array(false, false, false, '<div class="post-body">test-content</div>'),
			array(false, true, false, '<div class="post-body">test-content</div><p class="more"><a href="/news/archives/3#post-detail">≫ 続きを読む</a></p>'),
			array(false, false, 10, 'test-conte'),
		);
	}

/**
 * 詳細情報を取得する
 */
	public function testGetPostDetail() {
		$BlogPost = ClassRegistry::init('BlogPost');
		$post = $BlogPost->find('first', array('conditions' => array('BlogPost.id' => 1)));

		$result = $this->Blog->getPostDetail($post);
		$expects = $post['BlogPost']['detail'];
		$this->assertEquals($expects, $result);

		//30文字限定
		$options = array('cut' => 30);
		$result = $this->Blog->getPostDetail($post, $options);
		$expects = '詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま';
		$this->assertEquals($expects, $result);
	}

/**
 * 詳細情報を出力する cut option利用時
 */
	public function testPostDetailCut() {
		$BlogPost = ClassRegistry::init('BlogPost');
		$post = $BlogPost->find('first', array('conditions' => array('BlogPost.id' => 1)));

		$this->expectOutputString('詳細が入ります。詳細が入ります。詳細が入ります。詳細が入りま');

		//30文字限定
		$options = array(
			'cut' => 30
		);
		$this->Blog->postDetail($post, $options);
	}

/**
 * 記事が属するカテゴリ名を取得する
 */
	public function testGetCategory() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
//		$post = array('BlogCategory' => array(
//			'id' => 1,
//			'name' => 'release',
//			'title' => 'プレスリリース',
//		));
//		$this->Blog->getCategory($post);
	}

/**
 * タグを取得する
 * 
 * @dataProvider getTagDataProvider
 */
	public function testGetTag($options, $expects) {
		$post = array(
			'BlogTag' => array(
				array('name' => 'test1'),
				array('name' => 'test2'),
			),
			'BlogContent' => array(
				'name' => 'news'
			)
		);
		$result = $this->Blog->getTag($post, $options);
		$this->assertEquals($expects, $result, 'タグを正しく取得できません');
	}

	public function getTagDataProvider() {
		return [
			[['separator' => ' , '], '<a href="/news/archives/tag/test1">test1</a> , <a href="/news/archives/tag/test2">test2</a>'],
			[['tag' => false], [
				['name' => 'test1', 'url' => '/news/archives/tag/test1'],
				['name' => 'test2', 'url' => '/news/archives/tag/test2']
			]]	
		];
	}

/**
 * カテゴリ一覧へのURLを取得する
 *
 * @param int $blogCategoryId ブログカテゴリーID
 * @param int $named $options['named']の値
 * @param string $expected 期待値
 * @dataProvider getCategoryUrlDataProvider
 */
	public function testGetCategoryUrl($blogCategoryId, $named, $expected) {
		$options = array(
			'named' => $named,
		);
		$result = $this->Blog->getCategoryUrl($blogCategoryId, $options);
		$this->assertEquals($result, $expected, 'カテゴリ一覧へのURLを正しく取得できません');
	}

	public function getCategoryUrlDataProvider() {
		return array(
			array(1, array(), '/news/archives/category/release'),
			array(2, array(), '/news/archives/category/release/child'),
			array(3, array(), '/news/archives/category/child-no-parent'),
			array(1, array('test1', 'test2'), '/news/archives/category/release/test1/test2'),
		);
	}

/**
 * 登録日
 */
	public function testGetPostDate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$post = array('BlogPost' => array(
			'posts_date' => '2015-08-10 18:58:07'
		));
		$this->Blog->getPostDate($post);
	}

/**
 * カテゴリーの一覧をリストタグで取得する
 * 
 * @param int $depth 階層
 * @param boolean $count 件数を表示するかどうか
 * @param array $options オプション
 * @param string $expected 期待値
 * @dataProvider getCategoryListDataProvider
 */
	public function testGetCategoryList($depth, $count, $options, $expected) {
		$BlogCategory = ClassRegistry::init('Blog.BlogCategory');
		$categories = $BlogCategory->getCategoryList(1, array('viewCount' => true, 'depth' => 3));
		$result = $this->Blog->getCategoryList($categories, $depth, $count, $options);
		$this->assertEquals($result, $expected, 'カテゴリーの一覧をリストタグで正しく取得できません');
	}

	public function getCategoryListDataProvider() {
		return array(
			array(3, false, array(), '<ul class="depth-1"><li><a href="/news/archives/category/release">プレスリリース</a><ul class="depth-2"><li><a href="/news/archives/category/release/child">子カテゴリ</a></li></ul></li><li><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ</a></li></ul>'),
			array(1, false, array(), '<ul class="depth-1"><li><a href="/news/archives/category/release">プレスリリース</a></li><li><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ</a></li></ul>'),
			array(0, false, array(), ''),
			array(3, true, array(), '<ul class="depth-1"><li><a href="/news/archives/category/release">プレスリリース(1)</a><ul class="depth-2"><li><a href="/news/archives/category/release/child">子カテゴリ(2)</a></li></ul></li><li><a href="/news/archives/category/child-no-parent">親子関係なしカテゴリ(0)</a></li></ul>'),
		);
	}

/**
 * 前の記事へのリンクを出力する
 * 
 * @param int $blogContentId ブログコンテンツID
 * @param int $id 記事ID
 * @param int $posts_date 日付
 * @dataProvider prevLinkDataProvider
 */
	public function testPrevLink($blogContentId, $id, $posts_date, $expected) {
		$this->expectOutputString($expected);
		$post = array('BlogPost' => array(
			'blog_content_id' => $blogContentId,
			'id' => $id,
			'posts_date' => $posts_date
		));
		$this->Blog->prevLink($post);
	}

	public function prevLinkDataProvider() {
		return array(
			array(1, 4, '9000-08-10 18:58:07', '<a href="/news/archives/4" class="prev-link">≪ ４記事目</a>'),
			array(1, 3, '1000-08-10 18:58:07', ''),
			array(2, 2, '9000-08-10 18:58:07', '<a href="/news/archives/8" class="prev-link">≪ ８記事目</a>'),
			array(2, 1, '1000-08-10 18:58:07', ''),
		);
	}

/**
 * 次の記事へのリンクを出力する
 *
 * @param int $blogContentId ブログコンテンツID
 * @param int $id 記事ID
 * @param int $posts_date 日付
 * @dataProvider nextLinkDataProvider
 */
	public function testNextLink($blogContentId, $id, $posts_date, $expected) {
		$this->expectOutputString($expected);
		$post = array('BlogPost' => array(
			'blog_content_id' => $blogContentId,
			'id' => $id,
			'posts_date' => $posts_date
		));
		$this->Blog->nextLink($post);
	}

	public function nextLinkDataProvider() {
		return array(
			array(1, 1, '9000-08-10 18:58:07', ''),
			array(1, 2, '1000-08-10 18:58:07', '<a href="/news/archives/1" class="next-link">ホームページをオープンしました ≫</a>'),
			array(2, 3, '9000-08-10 18:58:07', ''),
			array(2, 4, '1000-08-10 18:58:07', '<a href="/news/archives/7" class="next-link">７記事目 ≫</a>'),
		);
	}

/**
 * ブログテンプレートを取得
 * 
 * @param string $theme テーマ名
 * @param array $expected 期待値
 * @dataProvider getBlogTemplatesDataProvider
 */
	public function testGetBlogTemplates($theme, $expected) {
		$this->Blog->BcBaser->siteConfig['theme'] = $theme;
		$result = $this->Blog->getBlogTemplates();
		$this->assertEquals($result, $expected, 'ブログテンプレートを正しく取得できません');
	}

	public function getBlogTemplatesDataProvider() {
		return array(
			array('nada-icons', array('default' => 'default'))
		);
	}

/**
 * 公開状態を取得する
 */
	public function testAllowPublish() {
		$data = array(
			'status' => true,
			'publish_begin' => '2015-08-10 18:58:07',
			'publish_end' => '9000-08-10 18:58:07'
		);
		$result = $this->Blog->allowPublish($data);
		$this->assertEquals($result, 1, '公開状態を正しく取得できません');

		$data['status'] = 0;
		$result = $this->Blog->allowPublish($data);
		$this->assertEquals($result, 0, '公開状態を正しく取得できません');
	}

/**
 * 記事中の画像を取得する
 * 
 * @param int $num 何枚目の画像か順番を指定
 * @param boolean $link 詳細ページへのリンクをつけるかどうか
 * @param array $expected 期待値
 * @dataProvider getPostImgDataProvider
 */
	public function testGetPostImg($num, $link, $expected) {
		$post = array('BlogPost' => array(
			'blog_content_id' => 1,
			'name' => 'test-name ',
			'content' => '<img src="test1.jpg"><img src="test2.jpg">',
			'detail' => '',
			'no' => '',
		));
		$options = array(
			'num' => $num,
			'link' => $link,
		);
		$result = $this->Blog->getPostImg($post, $options);
		$this->assertEquals($expected, $result, '記事中の画像を正しく取得できません');
	}

	public function getPostImgDataProvider() {
		return array(
			array(1, false, '<img src="/img/test1.jpg" alt="test-name "/>'),
			array(2, false, '<img src="/img/test2.jpg" alt="test-name "/>'),
			array(1, true, '<a href="/news/archives/"><img src="/img/test1.jpg" alt="test-name "/></a>'),
			array(3, false, ''),
		);
	}

/**
 * 記事中のタグで指定したIDの内容を取得する
 */
	public function testGetHtmlById() {
		$post = array('BlogPost' => array(
			'content' => '<p id="test-id1">test-content1</p><div id="test-id2">test-content1</div>',
			'detail' => '<p id="test-id1">test-content2</p>',
		));
		$result = $this->Blog->getHtmlById($post, 'test-id1');
		$expected = 'test-content1';
		$this->assertEquals($expected, $result, '記事中のタグで指定したIDの内容を正しく取得できません');
	}

/**
 * 親カテゴリを取得する
 */
	public function testGetParentCategory() {
		$message = '正しく親カテゴリーを取得できません';
		$post = array('BlogCategory' => array('id' => 1));
		$result = $this->Blog->getParentCategory($post);
		$this->assertEmpty($result, $message);

		$post['BlogCategory']['id'] = 2;
		$result = $this->Blog->getParentCategory($post);
		$this->assertEquals('release', $result['BlogCategory']['name'], $message);
	}


/**
 * 同じタグの関連投稿を取得する
 */
	public function testGetRelatedPosts() {
		$post = array(
			'BlogPost' => array(
				'id' => 1,
				'blog_content_id' => 1,
			),
			'BlogTag' => array(
				array('name' => '新製品')
			)
		);
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEquals($result[0]['BlogPost']['id'], 3, '同じタグの関連投稿を正しく取得できません');
		$this->assertEquals($result[1]['BlogPost']['id'], 2, '同じタグの関連投稿を正しく取得できません');

		$post['BlogPost']['id'] = 2;
		$post['BlogPost']['blog_content_id'] = 1;
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEquals($result[0]['BlogPost']['id'], 3, '同じタグの関連投稿を正しく取得できません');

		$post['BlogPost']['id'] = 7;
		$post['BlogPost']['blog_content_id'] = 2;
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEmpty($result, '関連していない投稿を取得しています');

		$post['BlogPost']['id'] = 2;
		$post['BlogPost']['blog_content_id'] = 3;
		$result = $this->Blog->getRelatedPosts($post);
		$this->assertEmpty($result, '関連していない投稿を取得しています');
	}

/**
 * アイキャッチ画像を取得する
 */
	public function testGetEyeCatch() {
		$post = array('BlogPost' => array(
			'blog_content_id' => 1,
			'eye_catch' => 'test-eye_catch.jpg'
		));
		$result = $this->Blog->getEyeCatch($post);
		$expected = '/\/files\/blog\/1\/blog_posts\/test-eye_catch.jpg/';

		$this->assertRegExp($expected, $result, 'アイキャッチ画像を正しく取得できません');
	}

/**
 * メールフォームプラグインのフォームへのリンクを生成する
 */
	public function testMailFormLink() {
		$this->expectOutputString('<a href="/test-contentsName">test-title</a>');
		$this->Blog->mailFormLink('test-title', 'test-contentsName');
	}

/**
 * カテゴリ取得
 */
	public function testGetCategories() {
		$this->loadFixtures('BlogCategoryTree');
		// １階層、かつ、siteId=0
		$categories = $this->Blog->getCategories();
		$this->assertEquals(1, count($categories));
		// サイトフィルター解除
		$categories = $this->Blog->getCategories(['siteId' => false]);
		$this->assertEquals(2, count($categories));
		// 深さ指定（子）
		$categories = $this->Blog->getCategories(['depth' => 2]);
		$this->assertEquals(1, count($categories[0]['BlogCategory']['children']));
		// 深さ指定（孫）
		$categories = $this->Blog->getCategories(['depth' => 3]);
		$this->assertEquals(1, count($categories[0]['BlogCategory']['children'][0]['BlogCategory']['children']));
		// ブログコンテンツID指定
		$categories = $this->Blog->getCategories(['siteId' => null, 'blogContentId' => 1]);
		$this->assertEquals(1, count($categories));
		// 並べ替え指定
		$categories = $this->Blog->getCategories(['siteId' => null, 'order' => 'BlogCategory.name']);
		$this->assertEquals(4, $categories[0]['BlogCategory']['id']);
		// 親指定
		$categories = $this->Blog->getCategories(['parentId' => 2]);
		$this->assertEquals(3, $categories[0]['BlogCategory']['id']);
		// スレッド形式
		$categories = $this->Blog->getCategories(['threaded' => true]);
		$this->assertEquals(3, $categories[0]['children'][0]['children'][0]['BlogCategory']['id']);
		// ID指定
		$categories = $this->Blog->getCategories(['id' => 3]);
		$this->assertEquals('孫カテゴリ', $categories[0]['BlogCategory']['title']);
	}

/**
 * 子カテゴリを持っているかどうか
 *
 * BlogCategory::hasChild() のラッピングの為、テストはスルー 
 */
	public function testHasChildCategory() {
		$this->markTestIncomplete('このメソッドは、BlogCategory::hasChild() をラッピングしているメソッドの為スキップします。');
	}

/**
 * タグリストを取得する
 * 
 * @param mixed $expected
 * @param mixed $name
 * @param array $options
 * @dataProvider getTagListDataProvider
 */
	public function testGetTagList($expected, $name, $options = []) {
		$this->loadFixtures('BlogPostBlogTagFindCustomPrams');
		$this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogContentBlogTagFindCustomPrams');
		$this->loadFixtures('ContentBlogTagFindCustomPrams');
		$result = $this->Blog->getTagList($name, $options);
		if($result) {
			$result = Hash::extract($result, '{n}.BlogTag.name');	
		}
		$this->assertEquals($expected, $result);
	}
	
	public function getTagListDataProvider() {
		return [
			[['タグ１'], 'blog1'],
			[['タグ１', 'タグ２'], 'blog2'],
			[['タグ１', 'タグ２', 'タグ３', 'タグ４', 'タグ５'], null],
			[['タグ１', 'タグ２', 'タグ３'], null, ['siteId' => 2]],
			[['タグ１', 'タグ２', 'タグ３'], ['/s/blog3/']],
		];
	}

/**
 * タグリストを出力する
 * 
 * @param string $expected
 * @param mixed $name
 * @param array $options
 * @dataProvider tagListDataProvider
 */
	public function testTagList($expected, $name, $options = []) {
		$this->loadFixtures('BlogPostBlogTagFindCustomPrams');
		$this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogContentBlogTagFindCustomPrams');
		$this->loadFixtures('ContentBlogTagFindCustomPrams');
		$this->expectOutputRegex($expected);
		$this->Blog->tagList($name, $options);
	}
	
	public function tagListDataProvider() {
		return [
			['/(?=\/tag\/タグ１).*?(?!.*\/tag\/タグ２).*?(?!.*\/tag\/タグ３)/s', 'blog1'],
			['/(?=\/tag\/タグ１).*?(?=\/tag\/タグ２).*?(?=\/tag\/タグ３)/s', '/s/blog3/'],
			['/(?=\/tags\/タグ１).*?(?=\/tags\/タグ２).*?(?=\/tags\/タグ３).*?(?=\/tags\/タグ４).*?(?=\/tags\/タグ５)/s', null],
			['/(?=\/tag\/タグ１).*?\(4\)/s', 'blog1', ['postCount' => true]],
		];
	}

/**
 * ブログタグ記事一覧へのリンクURLを取得する
 *
 * @param string $expected
 * @param int $blogContentId
 * @param string $name
 * @dataProvider getTagLinkUrlDataProvider
 */
	public function testGetTagLinkUrl($expected, $currentUrl, $blogContentId, $name) {
		$this->Blog->request = $this->_getRequest($currentUrl);
		$this->loadFixtures('BlogPostBlogTagFindCustomPrams');
		$this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogContentBlogTagFindCustomPrams');
		$this->loadFixtures('ContentBlogTagFindCustomPrams');
		$url = $this->Blog->getTagLinkUrl($blogContentId, ['BlogTag' => ['name' => $name]]);
		$this->assertEquals($expected, $url);
	}

	public function getTagLinkUrlDataProvider() {
		return [
			['/news/archives/tag/タグ１', '/', 1, 'タグ１'],
			['/s/blog3/archives/tag/タグ２', '/s/', 3, 'タグ２'],
			['/tags/タグ１', '/', null, 'タグ１'],
			['/s/tags/タグ２', '/s/', null, 'タグ２']
		];
	}

/**
 * タグ記事一覧へのリンクタグを取得する
 *
 * @param string $expected
 * @param string $currentUrl
 * @param int $blogContentId
 * @param $name
 * @dataProvider getTagLinkDataProvider
 */
	public function testGetTagLink($expected, $currentUrl, $blogContentId, $name) {
		$this->Blog->request = $this->_getRequest($currentUrl);
		$this->loadFixtures('BlogPostBlogTagFindCustomPrams');
		$this->loadFixtures('BlogPostsBlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogTagBlogTagFindCustomPrams');
		$this->loadFixtures('BlogContentBlogTagFindCustomPrams');
		$this->loadFixtures('ContentBlogTagFindCustomPrams');
		$url = $this->Blog->getTagLink($blogContentId, ['BlogTag' => ['name' => $name]]);
		$this->assertEquals($expected, $url);
	}

	public function getTagLinkDataProvider() {
		return [
			['<a href="/news/archives/tag/タグ１">タグ１</a>', '/', 1, 'タグ１'],
			['<a href="/s/blog3/archives/tag/タグ２">タグ２</a>', '/s/', 3, 'タグ２'],
			['<a href="/tags/タグ１">タグ１</a>', '/', null, 'タグ１'],
			['<a href="/s/tags/タグ２">タグ２</a>', '/s/', null, 'タグ２']
		];
	}

/**
 * タグ記事一覧へのリンクタグを出力する
 */
	public function testTagLink() {
		$this->markTestIncomplete('このメソッドは、BlogHelper::getTagLink() をラッピングしているメソッドの為スキップします。');
	}

/**
 * ブログ記事一覧出力
 *
 * @param string | array $contentsName 管理システムで指定したコンテンツ名
 * @param int $num 記事件数
 * @param array $options オプション
 * @param expected string 期待値
 * @param message string テスト失敗時に表示されるメッセージ
 * @dataProvider postsDataProvider
 */
	public function testPosts($currentUrl, $contentsName, $num, $options, $expected, $message = null) {
		$this->loadFixtures('BlogPostBlogBaserHelper', 'BlogPostsBlogTag');
		$this->View->loadHelper('BcTime');
		$url = null;
		if($contentsName) {
			if(!is_array($contentsName)) {
				$contentsName = [$contentsName];
			}
			$url = '/' . preg_replace("/^\/?(.*?)\/?$/", "$1", $contentsName[0]) . '/';
		}
		if($currentUrl) {
			$this->Blog->request = $this->_getRequest($currentUrl);
		}
		$this->expectOutputRegex($expected);
		$this->Blog->posts($contentsName, $num, $options);
	}

	public function postsDataProvider() {
		return [
			['', 'news', 5, [], '/name1.*name2.*name3/s', '記事が出力されません'], // 通常
			['', 'news2', 5, [], '/(?=no-data)/', '存在しないコンテンツが存在しています'],	// 存在しないコンテンツ
			['', 'news', 2, [], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // 件数指定
			['', 'news', 5, ['category' => 'release'], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'], // カテゴリ指定（子カテゴリあり）
			['', 'news', 5, ['category' => 'child'], '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のカテゴリを正しく指定できません'], // カテゴリ指定(子カテゴリなし)
			['', 'news', 5, ['tag' => '新製品'], '/^(?!.*name3).*(?!.*name1).*(?=name2).*/s', '記事のタグを正しく指定できません'], // tag指定
			['', 'news', 5, ['tag' => 'テスト'], '/記事がありません/', '記事のタグを正しく指定できません'], // 存在しないtag指定
			['', 'news', 5, ['year' => '2016'], '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の年を正しく指定できません'], // 年指定
			['', 'news', 5, ['year' => '2017'], '/^(?!.*name3).*(?!.*name2).*(?=name1).*/s', '記事の年を正しく指定できません'], // 年指定
			['', 'news', 5, ['year' => '2999'], '/記事がありません/', '記事の年を正しく指定できません'], // 記事がない年指定
			['', 'news', 5, ['month' => '2'], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の月を正しく指定できません'], // 月指定
			['', 'news', 5, ['day' => '2'], '/^(?!.*name1).*(?=name2).*(?=name3).*/s', '記事の日を正しく指定できません'], // 日指定
			['', 'news', 5, ['year' => '2016', 'month' => '02', 'day' => '02'], '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事の年月日を正しく指定できません'], // 年月日指定
			['', 'news', 5, ['id' => 2], '/^(?!.*name1).*(?!.*name3).*(?=name2).*/s', '記事のIDを正しく指定できません'], // ID指定
			['', 'news', 5, ['id' => 99], '/記事がありません/', '記事のIDを正しく指定できません'], // 存在しないID指定
			['', 'news', 5, ['keyword' => '1'], '/^(?!.*name2).*(?!.*name3).*(?=name1).*/s', '記事のキーワードを正しく指定できません'], // キーワード指定
			['', 'news', 5, ['keyword' => 'content'], '/name1.*name2.*name3/s', '記事のキーワードを正しく指定できません'], // キーワード指定
			['', null, 5, ['contentsTemplate' => 'default'], '/name1.*name2.*name3/s', 'contentsTemplateを正しく指定できません'], // contentsTemplateを指定
			['', 'news', 5, ['template' => 'archives'], '/プレスリリース/s', 'templateを正しく指定できません'], // template指定
			['', 'news', 5, ['direction' => 'ASC'], '/name3.*name2.*name1/s', 'templateを正しく指定できません'], // 昇順指定
			['', 'news', 5, ['direction' => 'DESC'], '/name1.*name2.*name3/s', 'templateを正しく指定できません'], // 降順指定
			['', 'news', 5, ['sort' => 'posts_date', 'direction' => 'ASC'], '/name3.*name2.*name1/s', 'sortを正しく指定できません'], // modifiedでソート
			['', 'news', 2, ['page' => 1], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', 'pageを正しく指定できません'], // ページ指定
			['', 'news', 2, ['page' => 2], '/^.+?<span class=\"title\">(?!.*name1).*(?!.*name2).*(?=name3).*/s', 'pageを正しく指定できません'], // ページ指定
			['/s/', 'news', 2, ['page' => 2], '/^.+?<span class=\"title\">name3<\/span>.*/s', 'pageを正しく指定できません'], // ページ指定
			['/service', 'news', 2, [], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 失敗
			['/news/', '', 2, ['contentsTemplate' => 'default'], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 成功
			['/s/news/', 'news', 2, [], '/^(?!.*name3).*(?=name1).*(?=name2).*/s', '記事の件数を正しく指定できません'], // autoSetCurrentBlog 成功
		];
	}

/**
 * 全ブログコンテンツの基本情報を取得する
 *
 * @return void
 */
	public function testGetContents() {
		// 復数ブログのデータを取得
		$this->loadFixtures('ContentMultiBlog', 'BlogPostBlogBaserHelper');

		// 全件取得
		$blogs = $this->Blog->getContents();
		$this->assertEquals(3, count($blogs));
		$this->assertEquals(2, $blogs[0]['Content']['id']);
		// デフォルトでは記事数を取得しない
		$this->assertFalse(isset($blogs[0]['BlogContent']['post_count']));

		// ソート順を変更
		$options = [
			'sort' => 'Content.id DESC',
			'siteId' => 0
		];
		$blogs = $this->Blog->getContents('', $options);
		$this->assertEquals(3, $blogs[0]['Content']['id']);

		// 記事数を取得
		$options = [
			'postCount' => true,
		];
		$blogs = $this->Blog->getContents('', $options);
		$this->assertEquals(3, $blogs[0]['BlogContent']['post_count']);
		$this->assertEquals(0, $blogs[1]['BlogContent']['post_count']);

		// ブログ指定 1つなので、配列に梱包されてない
		$blogs = $this->Blog->getContents('news');
		$this->assertEquals('news', $blogs['Content']['name']);

		// IDで取得
		$blogs = $this->Blog->getContents(2);
		$this->assertEquals('topics', $blogs['Content']['name']);

		// 復数指定取得
		$blogs = $this->Blog->getContents(['topics', 'news']);
		$this->assertEquals(2, count($blogs));
	}

/**
 * 現在のページがブログプラグインかどうかを判定する
 *
 * @param bool $expected 期待値
 * @param string $url リクエストURL
 * @return void
 * @dataProvider isBlogDataProvider
 */
	public function testIsBlog($expected, $url) {
		$this->Blog->request = $this->_getRequest($url);
		$this->assertEquals($expected, $this->Blog->isBlog());
	}

	public function isBlogDataProvider() {
		return [
			//PC
			[false, '/'],
			[false, '/index'],
			[false, '/contact/index'],
			[true, '/news/index'],
			// モバイルページ
			[false, '/m/'],
			[false, '/m/index'],
			[false, '/m/contact/index'],
			[true, '/m/news/index'],
			// スマートフォンページ
			[false, '/s/'],
			[false, '/s/index'],
			[false, '/s/contact/index'],
			[true, '/s/news/index']
		];
	}
	
}
