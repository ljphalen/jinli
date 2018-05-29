package com.gionee.game.search.server.similarities;

import org.apache.lucene.search.similarities.DefaultSimilarity;

public class PinyinSimilarity extends DefaultSimilarity {
	@Override
	public float tf(float freq) {
		return 1.0f;
	}
	
	@Override
	public float idf(long docFreq, long numDocs) {
		return 1.0f;
	}
}
