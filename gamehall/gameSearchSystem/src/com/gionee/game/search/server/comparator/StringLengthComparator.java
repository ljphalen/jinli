package com.gionee.game.search.server.comparator;

import java.util.Comparator;

public class StringLengthComparator implements Comparator<String> {
	public int compare(String o1, String o2) {
		int result = o2.length() > o1.length() ? 1 : (o2.length() == o1
				.length() ? 0 : -1);
		if (result == 0) {
			result = o1.compareTo(o2);
		}
		return result;
	}
}
