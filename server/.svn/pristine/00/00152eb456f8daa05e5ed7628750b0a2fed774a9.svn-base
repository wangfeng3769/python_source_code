<?php
define("IDX_TAG", 0);
define("IDX_ATTRIBUTES", 1);
define("IDX_TYPE", 2);
define("IDX_VALUE", 3);
define("IDX_CHILDREN", 4);
define("IDX_OBJ", 5);

define("PATH_IDX_NODE_LIST", 20);

define("TYPE_CDATA", 11);
define("TYPE_NODE", 12);

define("XMLERR_OK", 0);

/**
 * 节点。<note>value</note>这段xml里有两个节点，一个是noteType为TYPE_NODE的节点，
 * 没有值，但其下有一个子节点，值为value，noteType为TYPE_CDATA。
 *
 */
class Node {
	protected $m_tagIndex = array();
	protected $m_idIndex = array();
	protected $m_path = array();

	protected $m_nodeArr = array();

	private $m_currentIndex = -1;

	/**
	 *
	 * @param array $tagIndex
	 * @param array $idIndex
	 * @param array $path
	 * @param array $nodeArr
	 * @param int $currentIndex
	 */
	function __construct(&$tagIndex, &$idIndex, &$path, &$nodeArr, $currentIndex) {
		if (!is_array($tagIndex) || !is_array($idIndex) || !is_array($path) || !is_array($nodeArr) || !is_int($currentIndex)) {
			assert(false);
		}

		$this -> m_tagIndex = $tagIndex;
		$this -> m_idIndex = $idIndex;
		$this -> m_path = $path;
		$this -> m_nodeArr = $nodeArr;
		$this -> m_currentIndex = $currentIndex;
	}

	/**
	 * 取得节点类型，节点类型有TYPE_NODE和TYPE_CDATA两种。
	 *
	 * @return int
	 */
	public function getNodeType() {
		return $this -> m_nodeArr[$this -> m_currentIndex][IDX_TYPE];
	}

	/**
	 * 取得该节点指定属性的值
	 *
	 * @param string $attrName 属性名
	 * @return string
	 */
	public function getAttr($attrName) {
		if (!empty($attrName)) {
			return $this -> m_nodeArr[$this -> m_currentIndex][IDX_ATTRIBUTES][$attrName];
		}
		return "";
	}

	public function getAttrArr() {
		$this -> m_nodeArr[$this -> m_currentIndex][IDX_ATTRIBUTES];
	}

	/**
	 * 取得节点的值。如果节点为TYPE_NODE，将取其子节点为TYPE_CDATA节点的值。
	 *
	 * @param string $tag
	 * @param int $index
	 * @return string
	 */
	public function getValue($path = "", $index = 0) {
		$val = "";

		if ("" == $path)//如果为空，就取本节点的值
		{
			//如果本节点是TYPE_NODE，就取子节点的值。
			if (TYPE_NODE == $this -> m_nodeArr[$this -> m_currentIndex][IDX_TYPE]) {
				$i = 0;
				foreach ($this->m_nodeArr[$this->m_currentIndex][IDX_CHILDREN] as $structNode) {
					if (TYPE_CDATA == $this -> m_nodeArr[$structNode][IDX_TYPE]) {
						if ($i == $index) {
							$val = $this -> m_nodeArr[$structNode][IDX_VALUE];
							break;
						}
						$i++;
					}
				}
			} else {
				$val = $this -> m_nodeArr[$this -> m_currentIndex][IDX_VALUE];
			}
		} else {
			$node = $this -> getNode($path, $index);
			if (NULL != $node) {
				$val = $node -> getValue();
			}
		}
		return $val;
	}

	/**
	 * 取得子节点数组。
	 *
	 * @return Node[]
	 */
	public function getChildNodeArr() {
		$nodeArr = array();

		$structNode = &$this -> m_nodeArr[$this -> m_currentIndex];
		foreach ($structNode[IDX_CHILDREN] as $key) {
			if (NULL == $structNode[IDX_OBJ]) {
				$structNode[IDX_OBJ] = new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_path, $this -> m_nodeArr, $key);
			}
			$nodeArr[] = $structNode[IDX_OBJ];
		}

		return $nodeArr;
	}

	/**
	 * 取得子节点
	 *
	 * @param int $index
	 * @return Node
	 */
	public function getChildNode($index = 0) {
		$structNode = &$this -> m_nodeArr[$this -> m_currentIndex];
		if (count($structNode[IDX_CHILDREN]) > 0) {
			if (NULL == $structNode[IDX_OBJ]) {
				$structNode[IDX_OBJ] = new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_path[$this -> m_nodeArr[$structNode[IDX_CHILDREN][$index]][IDX_TAG]], $this -> m_nodeArr, $structNode[IDX_CHILDREN][$index]);
			}
			return $structNode[IDX_OBJ];
		}
		return NULL;
	}

	/**
	 * 取得父节点
	 *
	 * @return Node
	 */
	public function getParentNode() {
		$i = $this -> m_currentIndex;
		while ($i > 0) {
			$structNode = &$this -> m_nodeArr[$i];
			if (TYPE_NODE == $structNode[IDX_TYPE]) {
				foreach ($structNode[IDX_CHILDREN] as $childIndex) {
					if ($this -> m_currentIndex == $childIndex) {
						if (NULL == $structNode[IDX_OBJ]) {
							$structNode[IDX_OBJ] = new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_path, $this -> m_nodeArr, $i);
						}
						return $structNode[IDX_OBJ];
					}
				}
			}
			$i--;
		}
		return NULL;
	}

	/**
	 * 取得指定path的所有节点
	 *
	 * @param string $path以“::”分隔，用tag组成的路径。
	 * @return Node[]
	 */
	public function getNodeArr($path, $index = -1) {
		$nodeArr = array();

		$tagArr = explode("::", $path);

		$i = 0;
		$currentPath = &$this -> m_path[$tagArr[$i]];
		for ($i = 1; $i < count($tagArr); $i++) {
			if (null == $currentPath) {
				break;
			}
			$currentPath = &$currentPath[$tagArr[$i]];
		}

		if (null != $currentPath)//有可能指定的path存在。
		{
			if (-1 == $index) {
				$start = 0;
				$end = count($currentPath[PATH_IDX_NODE_LIST]);
			} else {
				$start = $index;
				$end = $index + 1;
			}

			for ($i = $start; $i < $end; $i++) {
				if (NULL == $this -> m_nodeArr[$currentPath[PATH_IDX_NODE_LIST][$i]][IDX_OBJ]) {
					$this -> m_nodeArr[$currentPath[PATH_IDX_NODE_LIST][$i]][IDX_OBJ] = new Node($this -> m_tagIndex, $this -> m_idIndex, $currentPath, $this -> m_nodeArr, $currentPath[PATH_IDX_NODE_LIST][$i]);
				}
				$nodeArr[] = $this -> m_nodeArr[$currentPath[PATH_IDX_NODE_LIST][$i]][IDX_OBJ];
			}
		}

		return $nodeArr;
	}

	/**
	 * 取得指定path的节点
	 *
	 * @param string $path以“::”分隔，用tag组成的路径。
	 * @return Node
	 */
	public function getNode($path, $index = 0) {
		$nodeArr = $this -> getNodeArr($path, $index);
		return $nodeArr[0];
	}

}

class Dom extends Node {
	private $m_parser = NULL;

	private $m_nodeIndex = -1;
	private $m_nodeStack = array();
	private $m_pathStack = array();
	//用于记录一个节点的character，因为xml在解析的时候，遇到&amp;等字符会停止，然后再次调用character方法。也就是说如果character里有&amp;，
	//会把其拆成几个character解析。
	private $m_currentCharacter = "";

	public function __construct() {
	}

	/**
	 * 装载xml字符并解析
	 *
	 * @param string $xml
	 */
	public function loadXml($xml) {
		if (NULL == $this -> m_parser) {
			$this -> m_parser = xml_parser_create();

			xml_parser_set_option($this -> m_parser, XML_OPTION_CASE_FOLDING, XML_ERROR_NONE);
			xml_set_object($this -> m_parser, $this);
			xml_set_element_handler($this -> m_parser, "elementStart", "elementEnd");
			xml_set_character_data_handler($this -> m_parser, "character");
		}
		xml_parse($this -> m_parser, $xml);
	}

	public function getErrno() {
		return @xml_get_error_code($this -> m_parser);
	}

	public function getErrstr() {
		return @xml_error_string(xml_get_error_code($this -> m_parser));
	}

	protected function elementStart($p, $tag, $attrArr) {
		//1.如果这个元素开始之前记录character的变量有值，说明父节点的文本节点结束了。
		if ("" != $this -> m_currentCharacter) {
			$this -> characterNode();
		}

		$this -> m_nodeIndex++;

		//2.组节点
		$this -> m_nodeArr[$this -> m_nodeIndex] = array(IDX_TAG => $tag, IDX_ATTRIBUTES => $attrArr, IDX_TYPE => TYPE_NODE, IDX_CHILDREN => array());

		//3.加入父节点的子节点属性
		if (count($this -> m_nodeStack) > 0) {
			$this -> m_nodeArr[$this -> m_nodeStack[count($this -> m_nodeStack) - 1]][IDX_CHILDREN][] = $this -> m_nodeIndex;
		}

		//4.入栈
		$this -> m_nodeStack[] = $this -> m_nodeIndex;

		//5.组tag索引
		$this -> m_tagIndex[$tag][] = $this -> m_nodeIndex;

		//6.组id索引
		if (NULL != $attrArr['id']) {
			$this -> m_idIndex[$attrArr['id']] = $this -> m_nodeIndex;
		}

		//7.组path
		if (count($this -> m_pathStack) > 0) {
			$this -> m_pathStack[count($this -> m_pathStack) - 1][$tag][PATH_IDX_NODE_LIST][] = $this -> m_nodeIndex;
			$this -> m_pathStack[] = &$this -> m_pathStack[count($this -> m_pathStack) - 1][$tag];
		} else {
			$this -> m_path[$tag][PATH_IDX_NODE_LIST][] = $this -> m_nodeIndex;
			$this -> m_pathStack[] = &$this -> m_path[$tag];
		}
	}

	/**
	 * 解析节点字符的回调函数。该函数在碰到&amp;等符号时，一个节点的的character会被调用多次，所以该函数的运算应该越简单越好。
	 *
	 * @param resource $p
	 * @param string $cdata
	 */
	protected function character($p, $cdata) {
		$this -> m_currentCharacter .= $cdata;
	}

	protected function elementEnd($p, $tag) {
		if ("" != $this -> m_currentCharacter) {
			$this -> characterNode();
		}

		array_pop($this -> m_nodeStack);
		array_pop($this -> m_pathStack);
	}

	private function characterNode() {
		if ("" != $this -> m_currentCharacter) {
			//有这样的xml<root>aaa<a>b</a>bbb<b>b</b>ccc</root>，所以当<a>开始时，应该把记录aaa的变量清除，
			//因为这时root没有结束，所以记录aaa的变量没有清除，而这个变量属于栈中最后一个节点的。
			$this -> m_nodeIndex++;

			$this -> m_nodeArr[$this -> m_nodeIndex] = array(IDX_TAG => &$this -> m_nodeArr[$this -> m_nodeStack[count($this -> m_nodeStack) - 1]][IDX_TAG], IDX_TYPE => TYPE_CDATA, IDX_VALUE => $this -> m_currentCharacter);
			$this -> m_currentCharacter = "";

			$this -> m_nodeArr[$this -> m_nodeStack[count($this -> m_nodeStack) - 1]][IDX_CHILDREN][] = $this -> m_nodeIndex;
		}
	}

}
?>