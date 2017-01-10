<?php
require 'vendor/autoload.php';
require 'Tool.php';

class EvernoteModel
{
	protected $token;
	protected $tag; 
	protected $notebook;

	protected $sandbox = false;
	protected $china   = false;

	public function __construct($config)
	{
		$this->token = $config['token'];
		$this->tag   = $config['tag'];
		$this->notebook = $config['notebook'];
	}
	/**
	 * get base client instance
	 * @author bignerd
	 * @since  2017-01-09T11:43:36+0800
	 */
	protected function getClient()
	{
		$client = new \Evernote\Client($this->token, $this->sandbox, null, null, $this->china);
		return $client;
	}
	/**
	 * 添加一条笔记记录
	 * @author bignerd
	 * @since  2017-01-09T15:46:53+0800
	 */
	public function add($content='')
	{
		$title = $this->noteTitle();
		$guid = $this->noteExist();
		$content = Tool::handleSaveContent($content);
		
		if($guid){
			return $this->updateNote($guid, $content);
		}else{
			return $this->createNote($title, $content, $this->tag);
		}
	}
	/**
	 * 笔记的默认标题
	 * @author bignerd
	 * @since  2017-01-09T15:28:17+0800
	 */
	public function noteTitle()
	{
		date_default_timezone_set('PRC');
		return 'Bignerd record '.date('Y/m').' 第'.Tool::weekIndexOfMonth().'周';
	}
	/**
	 * 判断本周的笔记是否已经创建
	 * @author bignerd
	 * @since  2017-01-09T15:35:27+0800
	 */
	public function noteExist()
	{
		session_start();

		$guid = '';
		$title = $this->noteTitle();

		if($guid = $this->getSession()){
			return $guid;
		}else if($guid = $this->searchNote($title)){
			$this->setSession($guid);
			return $guid;
		}else{
			return false;
		}

	}
	/**
	 * 搜索笔记
	 * @author bignerd
	 * @since  2017-01-09T11:51:49+0800
	 * @param  string $title 笔记标题
	 * @param  string $notebookName 笔记本名称
	 */
	public function searchNote($title = '', $notebookName = 'mind')
	{
		$client = $this->getClient();
		/**
		 * The search string
		 */
		$search = new \Evernote\Model\Search($title);

		/**
		 * The notebook to search in
		 */
		$notebook = new \Evernote\Model\Notebook($notebookName);

		/**
		 * The scope of the search
		 */
		$scope = \Evernote\Client::SEARCH_SCOPE_PERSONAL;

		/**
		 * The order of the sort
		 */
		$order = \Evernote\Client::SORT_ORDER_REVERSE | \Evernote\Client::SORT_ORDER_RECENTLY_CREATED;

		/**
		 * The number of results
		 */
		$maxResult = 1;

		$results = $client->findNotesWithSearch($search, $notebook, $scope, $order, $maxResult);

		$noteGuid = '';

		foreach ($results as $result) {
		    $noteGuid    = $result->guid;
		    $noteType    = $result->type;
		    $noteTitle   = $result->title;
		    $noteCreated = $result->created;
		    $noteUpdated = $result->updated;
		}
		return $noteGuid;
	}
	/**
	 * 获取当前笔记中的笔记内容
	 * @author bignerd
	 * @since  2017-01-09T14:39:21+0800
	 * @param  [type] $guid
	 */
	public function getNoteData($guid)
	{
		$client = $this->getClient();
		$scope = \Evernote\Client::PERSONAL_SCOPE;
		$note = $client->getNote($guid, $scope);

		$noteContext = (string)($note->content->toEnml());
		$oldContext  = Tool::handleEvernoteContent($noteContext);
		$noteData = [
			'title' => $note->getTitle(),
			'plainText' => $oldContext,
			'noteObj' => $note,
		];
		return $noteData;
	}
	/**
	 * 创建笔记
	 * @author bignerd
	 * @since  2017-01-09T13:30:56+0800
	 * @param  string $title
	 * @param  string $content
	 * @param  array $tag
	 */
	public function createNote($title='', $content='', array $tag)
	{
		$client = $this->getClient();
		$note         = new \Evernote\Model\Note();
		$note->title  = $title;	
		$note->content = new \Evernote\Model\PlainTextNoteContent($content);
		$note->tagNames = $tag;

		$notebook = new \Evernote\Model\Notebook('mind');
		$saved = $client->uploadNote($note, $notebook);

		if($guid = $saved->getGuid()){
			$this->setSession($guid);
			echo '创建成功.';
		}else{
			echo '创建失败.';
		}
	}
	/**
	 * 更新笔记内容
	 * @author bignerd
	 * @since  2017-01-09T15:07:27+0800
	 * @param  [type] $guid
	 * @param  string $newContent
	 */
	public function updateNote($guid, $newContent='')
	{
		$client = $this->getClient();

		$existNoteData = $this->getNoteData($guid);

		$newContent = $existNoteData['plainText']."<br /><br />".$newContent;

		$note = new \Evernote\Model\Note();
		$note->title = $existNoteData['title'];
		$note->tagNames = $this->tag;
		$note->content = new \Evernote\Model\PlainTextNoteContent($newContent);

		$updated = $client->replaceNote($existNoteData['noteObj'], $note);

		echo ($updated)? '更新成功.' : '更新失败.';

	}
	public function getSession()
	{
		$title = $this->noteTitle();
		$sessionKey = 'guid_'.$title;
		return array_key_exists($sessionKey, $_SESSION)? $_SESSION[$sessionKey] : '';
	}
	public function setSession($guid)
	{
		$title = $this->noteTitle();
		$sessionKey = 'guid_'.$title;
		$_SESSION[$sessionKey] = $guid;
	}
}
?>