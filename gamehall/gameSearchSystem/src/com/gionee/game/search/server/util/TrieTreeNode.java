package com.gionee.game.search.server.util;

import java.util.HashMap;
import java.util.Map;

/**
 * 责任感也好，才能也好，都是有限度的。不论别人的期望多高，或如何强迫，不可能的事情永远也不可能。 ------杨威利
 * 
 * @author Leon.Chen 2009-5-25
 */
public class TrieTreeNode {
	public TrieTreeNode parent;
	public Map<Character, TrieTreeNode> childs = new HashMap<Character, TrieTreeNode>();
	public char value = 0;
	public int state = 0;
	public int count = 0;

	public boolean isDelFlg() {
		if (this.count > 0) {
			return false;
		}
		return true;
	}

	public String toString() {
		StringBuilder buf = new StringBuilder();
		buf.append("<node value = " + value + " state = " + state + " count = "
				+ count + " />");
		return buf.toString();
	}
}
