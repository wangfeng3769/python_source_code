<?php

class Node {

	//索引
	public static $IDX_TAG = 0;
	public static $IDX_ATTRIBUTES = 1;
	public static $IDX_TYPE = 2;
	public static $IDX_VALUE = 3;
	public static $IDX_CHILDREN = 4;
	/**
	 * 已经new好的Node对象。在刚解析完xml之后，nodeArr里除了Dom外，没有Node对象。
	 * 当用户操作某些成员方法后，就产生了Node对象，下次就直接返回该对象了。
	 */
	public static $IDX_OBJ = 5;

	/**
	 * 节点类型，分为数据节点和普通节点。比如:<note>value</note>这段xml里有两个节点，一个是noteType为self::$TYPE_NODE的节点，
	 * 没有值，但其下有一个子节点，值为value，noteType为self::$TYPE_CDATA。
	 */
	public static $TYPE_CDATA = 11;
	public static $TYPE_NODE = 12;

	//索引
	protected $m_tagIndex = array();
	protected $m_idIndex = array();

	/**
	 * 所有节点数组。每个数组是一个单独的节点，其结构为：
	 * array( self::$IDX_TAG , self::$IDX_ATTRIBUTES , self::$IDX_TYPE , self::$IDX_VALUE , self::$IDX_CHILDREN=>array() , self::$IDX_OBJ );
	 */
	protected $m_nodeArr = array();
	//本节点在m_nodeArr里的索引
	protected $m_currentIndex = -1;

	/**
	 *
	 * @param array $tagIndex
	 * @param array $idIndex
	 * @param array $path
	 * @param array $nodeArr
	 * @param int $currentIndex
	 */
	function __construct(&$tagIndex, &$idIndex, &$nodeArr, $currentIndex) {
		if (!is_array($tagIndex) || empty($tagIndex) || !is_array($idIndex) || !is_array($nodeArr) || empty($nodeArr) || !is_int($currentIndex) || $currentIndex < 0) {
			throw new Exception('', 1);
		}

		$this -> m_tagIndex = $tagIndex;
		$this -> m_idIndex = $idIndex;
		$this -> m_nodeArr = $nodeArr;
		$this -> m_currentIndex = $currentIndex;
		$this -> m_nodeArr[$this -> m_currentIndex][self::$IDX_OBJ] = $this;
	}

	/**
	 * 取得节点类型，节点类型有self::$TYPE_NODE和self::$TYPE_CDATA两种。
	 *
	 * @return int
	 */
	public function getNodeType() {
		return $this -> m_nodeArr[$this -> m_currentIndex][self::$IDX_TYPE];
	}

	/**
	 * 取得该节点指定属性的值
	 *
	 * @param string $attrName 属性名
	 * @return string
	 */
	public function getAttr($attrName) {
		if (empty($attrName)) {

		}
		if (!empty($attrName)) {
			return $this -> m_nodeArr[$this -> m_currentIndex][self::$IDX_ATTRIBUTES][$attrName];
		}
		return "";
	}

	public function getAttrArr() {
		$this -> m_nodeArr[$this -> m_currentIndex][self::$IDX_ATTRIBUTES];
	}

	/**
	 * 取得节点的值。如果节点为self::$TYPE_NODE，将取其子节点为self::$TYPE_CDATA节点的值。
	 *
	 * @param string $tag
	 * @param int $index
	 * @return string
	 */
	public function getValue($path = "", $index = 0) {
		$val = "";

		if ("" == $path)//如果为空，就取本节点的值
		{
			//如果本节点是self::$TYPE_NODE，就取子节点的值。
			if (self::$TYPE_NODE == $this -> m_nodeArr[$this -> m_currentIndex][self::$IDX_TYPE]) {
				$i = 0;
				foreach ($this->m_nodeArr[$this->m_currentIndex][self::$IDX_CHILDREN] as $structNode) {
					if (self::$TYPE_CDATA == $this -> m_nodeArr[$structNode][self::$IDX_TYPE]) {
						if ($i == $index) {
							$val = $this -> m_nodeArr[$structNode][self::$IDX_VALUE];
							break;
						}
						$i++;
					}
				}
			} else {
				$val = $this -> m_nodeArr[$this -> m_currentIndex][self::$IDX_VALUE];
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
		foreach ($structNode[self::$IDX_CHILDREN] as $key) {
			if (NULL == $structNode[IDX_OBJ]) {
				$nodeArr[] = new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_nodeArr, $key);
			} else {
				$nodeArr[] = $structNode[IDX_OBJ];
			}
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
		if (count($structNode[self::$IDX_CHILDREN]) > 0) {
			if (NULL == $structNode[IDX_OBJ]) {
				return new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_nodeArr, $structNode[self::$IDX_CHILDREN][$index]);
			}
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
			if (self::$TYPE_NODE == $structNode[self::$IDX_TYPE]) {
				foreach ($structNode[self::$IDX_CHILDREN] as $childIndex) {
					if ($this -> m_currentIndex == $childIndex) {
						if (NULL == $structNode[IDX_OBJ]) {
							return new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_nodeArr, $i);
						}
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

		$tagArr = explode(">", $path);

		$parentNodeIndexArr = array($this -> m_currentIndex);
		foreach ($tagArr as $tag) {
			$nodeIndexArr = $this -> m_tagIndex[$tag];
			if (!is_array($nodeIndexArr)) {
				continue;
			}

			$newParentNodeIndexArr = array();
			foreach ($nodeIndexArr as $ni) {
				foreach ($parentNodeIndexArr as $pi) {
					if (in_array($ni, $this -> m_nodeArr[$pi][self::$IDX_CHILDREN])) {
						$newParentNodeIndexArr[] = $ni;
					}
				}
			}

			$parentNodeIndexArr = $newParentNodeIndexArr;
		}

		foreach ($parentNodeIndexArr as $ni) {
			if (null == $this -> m_nodeArr[$ni][self::$IDX_OBJ]) {
				$nodeArr[] = new Node($this -> m_tagIndex, $this -> m_idIndex, $this -> m_nodeArr, $ni);
			} else {
				$nodeArr[] = $this -> m_nodeArr[$ni][self::$IDX_OBJ];
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

		$this -> m_currentIndex = 0;
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
		$this -> m_nodeArr[$this -> m_nodeIndex] = array(self::$IDX_TAG => $tag, self::$IDX_ATTRIBUTES => $attrArr, self::$IDX_TYPE => self::$TYPE_NODE, self::$IDX_CHILDREN => array());

		//3.加入父节点的子节点属性
		if (count($this -> m_nodeStack) > 0) {
			$this -> m_nodeArr[$this -> m_nodeStack[count($this -> m_nodeStack) - 1]][self::$IDX_CHILDREN][] = $this -> m_nodeIndex;
		}

		//4.入栈
		$this -> m_nodeStack[] = $this -> m_nodeIndex;

		//5.组tag索引
		$this -> m_tagIndex[$tag][] = $this -> m_nodeIndex;

		//6.组id索引
		if (NULL != $attrArr['id']) {
			$this -> m_idIndex[$attrArr['id']] = $this -> m_nodeIndex;
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
	}

	private function characterNode() {
		if ("" != $this -> m_currentCharacter) {
			//有这样的xml<root>aaa<a>b</a>bbb<b>b</b>ccc</root>，所以当<a>开始时，应该把记录aaa的变量清除，
			//因为这时root没有结束，所以记录aaa的变量没有清除，而这个变量属于栈中最后一个节点的。
			$this -> m_nodeIndex++;

			$this -> m_nodeArr[$this -> m_nodeIndex] = array(self::$IDX_TAG => &$this -> m_nodeArr[$this -> m_nodeStack[count($this -> m_nodeStack) - 1]][self::$IDX_TAG], self::$IDX_TYPE => self::$TYPE_CDATA, self::$IDX_VALUE => $this -> m_currentCharacter);
			$this -> m_currentCharacter = "";

			$this -> m_nodeArr[$this -> m_nodeStack[count($this -> m_nodeStack) - 1]][self::$IDX_CHILDREN][] = $this -> m_nodeIndex;
		}
	}

}
?>