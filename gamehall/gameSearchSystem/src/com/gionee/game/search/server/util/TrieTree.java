package com.gionee.game.search.server.util;


/**
 * 如果我是生在太平时代，只不过是个初出茅庐的历史学者罢了，搞不好还是个默默无名的小人物呢。
 *                                                                   ------杨威利
 * @author Leon.Chen 2009-5-25
 */
public class TrieTree {

	public TrieTreeNode root = new TrieTreeNode();
	
	private String separator = "/";

	private static final int RECURSION_TIME = 5;

	private static int WORD_LEN = 0;
	
//	public List<String> result = new ArrayList<String>();

	/**
	 * @param word
	 */
	public void insertTrieTree(String word) {
		char[] wordChar = word.toCharArray();
		TrieTreeNode tempNode = root;
		for (int i = 0; i < wordChar.length; i++) {
			if (i < wordChar.length - 1) {
				addTrieTreeNode(wordChar, tempNode, i);
				tempNode = tempNode.childs.get(wordChar[i]);
			} else {
				addTrieTreeNode(wordChar, tempNode, i);
				tempNode.childs.get(wordChar[i]).state = 1;
			}
		}
	}

	/**
	 * @param wordChar
	 * @param tempNode
	 * @param charIndex
	 */
	private void addTrieTreeNode(char[] wordChar, TrieTreeNode tempNode,
			int charIndex) {
		if (tempNode.childs.get(wordChar[charIndex]) == null) {
			tempNode.childs.put(wordChar[charIndex], new TrieTreeNode());
			tempNode.childs.get(wordChar[charIndex]).value = wordChar[charIndex];
			tempNode.childs.get(wordChar[charIndex]).parent = tempNode;
		}
		tempNode.childs.get(wordChar[charIndex]).count++;
	}

	/**
	 * @param word
	 */
	public void deleteTrieTree(String word) {
		char[] wordChar = word.toCharArray();
		TrieTreeNode tempNode = root;
		for (int i = 0; i < wordChar.length; i++) {
			if (tempNode.childs.get(wordChar[i]) == null) {
				return;
			}
			tempNode = tempNode.childs.get(wordChar[i]);
		}

		tempNode.state = 0;

		for (int i = wordChar.length - 1; i >= 0; i--) {
			if (tempNode != root) {
				tempNode.count--;
			}
			if (!tempNode.isDelFlg()) {
				return;
			}
			tempNode = tempNode.parent;
			tempNode.childs.put(wordChar[i], null);
		}
	}

	/**
	 * @param text
	 */
	public synchronized String searchTrieTree(String text) {
		char[] textChar = text.toCharArray();
		TrieTreeNode tempNode = root;
		int backStep= 0;
        StringBuilder sb=new StringBuilder("");
		for (int i = 0; i < textChar.length; i++) {
			if (tempNode.childs.get(textChar[i]) != null) {
				if (tempNode.childs.get(textChar[i]).value == textChar[i]) {
					if (tempNode.childs.get(textChar[i]).state != 1) {
						WORD_LEN++;
						backStep++;
						tempNode = tempNode.childs.get(textChar[i]);
					} else {
						WORD_LEN++;
						backStep = 0;
						i = searchMaxWord(tempNode.childs.get(textChar[i]),
								textChar, i + 1);
						String str = constructWord(textChar, i, WORD_LEN);
//						result.add(str);
                        sb.append(str).append(separator);
//						System.out.println(str);
						tempNode = root;
						WORD_LEN = 0;
					}
				} else {
					tempNode = root;
					WORD_LEN = 0;
				}
			} else {
				while(backStep>0){
					i--;backStep--;
				}
				tempNode = root;
				WORD_LEN = 0;
			}
		}
        return sb.toString().trim();
	}
	
	/**
	 * @param textChar
	 * @param endIndex
	 * @param len
	 * @return
	 */
	private String constructWord(char[] textChar, int endIndex, int len) {
		int startIndex = endIndex + 1 - len;
        if(startIndex<0) {
            startIndex=0;
        }
		StringBuilder str = new StringBuilder();
		for (int i = startIndex; i <= endIndex; i++) {
//            System.out.println("i==" + i);
			str.append(textChar[i]);
		}
		return str.toString();
	}

	/**
	 * 
	 * 最大正向匹配改进
	 * 
	 * @param node
	 * @param textChar
	 * @param index
	 * @return
	 */
	private int searchMaxWord(TrieTreeNode node, char[] textChar, int index) {
		if (terminateCondition(node, textChar, index)) {
			return --index;
		}
		TrieTreeNode tempNode = node;
		for (int i = index; i < index + RECURSION_TIME; i++) {
			if (tempNode.childs.get(textChar[i]).state != 1) {
				WORD_LEN++;
				tempNode = tempNode.childs.get(textChar[i]);
			} else {
				WORD_LEN++;
				return searchMaxWord(tempNode.childs.get(textChar[i]),
						textChar, i + 1);
			}
		}
		return -1;
	}

	/**
	 * 改进算法递归终止条件
	 * 
	 * @param node
	 * @param textChar
	 * @param index
	 * @return
	 */
	private boolean terminateCondition(TrieTreeNode node, char[] textChar,
			int index) {
		TrieTreeNode tempNode = node;
		for (int i = index; i < index + RECURSION_TIME; i++) {
			if (i > textChar.length - 1) {
				return true;
			}
			if (tempNode.childs.get(textChar[i]) == null) {
				return true;
			}
			if (tempNode.childs.get(textChar[i]).state != 1) {
				tempNode = tempNode.childs.get(textChar[i]);
			} else {
				return false;
			}
		}
		return true;
	}
	
	public String toString(String word) {
		char[] c = word.toCharArray();
		TrieTreeNode tempNode = root;
		StringBuilder buf = new StringBuilder();
		for (int i = 0; i < c.length; i++) {
			TrieTreeNode currentNode = tempNode.childs.get(c[i]);
			if (currentNode != null) {
				buf.append(currentNode.toString());
				buf.append("\n");
			}
			tempNode = tempNode.childs.get(c[i]);
		}
		return buf.toString();
	}

	public String getSeparator() {
		return separator;
	}

	public void setSeparator(String separator) {
		this.separator = separator;
	}
}
