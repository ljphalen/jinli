package com.gionee.game.search.server.comparator;

import java.util.Comparator;

import com.gionee.game.search.server.model.KeyNum;

public class KeyNumComparator implements Comparator<KeyNum> {
	public int compare(KeyNum o1, KeyNum o2) {
		if (o2.getNum() > o1.getNum()) {
			return 1;
		}
		else if (o2.getNum() < o1.getNum()) {
			return -1;
		}
		
		return 0;
	}
}
